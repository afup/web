<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Category;
use AppBundle\Accounting\Entity\Event;
use AppBundle\Accounting\Entity\InvoicingPeriod;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Compta\Importer\AutoQualifier;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Transaction>
 */
final class TransactionRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getNextTransaction(int $transactionId): ?Transaction
    {
        $undeterminedCategory = $this->getEntityManager()->getReference(Category::class, AutoQualifier::DEFAULT_CATEGORIE);
        $undeterminedEvent = $this->getEntityManager()->getReference(Event::class, AutoQualifier::DEFAULT_EVENEMENT);

        return $this->createQueryBuilder('t')
            ->where('t.categorie = :undeterminedCategory OR t.evenement = :undeterminedEvent')
            ->andWhere('t.id > :transactionId')
            ->setParameter('undeterminedCategory', $undeterminedCategory)
            ->setParameter('undeterminedEvent', $undeterminedEvent)
            ->setParameter('transactionId', $transactionId)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getEntriesPerInvoicingPeriod(InvoicingPeriod $period, bool $onlyUnclasifedEntries, int $operationType = 0): array
    {
        $filtre = $operationType === 1 || $operationType === 2 ? ' AND compta.idoperation = :operationType ' : '';

        $sql = 'SELECT '
            . 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation, compta.id as idtmp, '
            . 'compta.comment, compta.attachment_required, compta.attachment_filename, '
            . 'compta_reglement.reglement, '
            . 'compta_evenement.evenement, '
            . 'compta_categorie.categorie, '
            . 'compta_compte.nom_compte, '
            . '(COALESCE(compta.montant_ht_soumis_tva_0,0) + COALESCE(compta.montant_ht_soumis_tva_5_5,0) + COALESCE(compta.montant_ht_soumis_tva_10, 0) + COALESCE(compta.montant_ht_soumis_tva_20, 0)) as montant_ht, '
            . '((COALESCE(compta.montant_ht_soumis_tva_5_5, 0)*0.055) + (COALESCE(compta.montant_ht_soumis_tva_10, 0)*0.1) + (COALESCE(compta.montant_ht_soumis_tva_20, 0)*0.2)) as montant_tva, '
            . 'compta.montant_ht_soumis_tva_0 as montant_ht_0, '
            . 'compta.montant_ht_soumis_tva_5_5 as montant_ht_5_5, '
            . 'compta.montant_ht_soumis_tva_5_5*0.055 as montant_tva_5_5, '
            . 'compta.montant_ht_soumis_tva_10 as montant_ht_10, '
            . 'compta.montant_ht_soumis_tva_10*0.1 as montant_tva_10, '
            . 'compta.montant_ht_soumis_tva_20 as montant_ht_20, '
            . 'compta.montant_ht_soumis_tva_20*0.2 as montant_tva_20, '
            . 'compta.tva_zone '
            . 'FROM compta '
            . 'LEFT JOIN compta_categorie on compta_categorie.id = compta.idcategorie '
            . 'LEFT JOIN compta_reglement on compta_reglement.id = compta.idmode_regl '
            . 'LEFT JOIN compta_evenement on compta_evenement.id = compta.idevenement '
            . 'LEFT JOIN compta_compte on compta_compte.id = compta.idcompte '
            . 'WHERE compta.date_ecriture >= :startDate '
            . 'AND compta.date_ecriture <= :endDate '
            . $filtre;

        if ($onlyUnclasifedEntries === true) {
            $sql .= ' AND (
                  compta_evenement.evenement = "A déterminer"
                OR
                  compta_categorie.categorie = "A déterminer"
                OR
                  compta_reglement.reglement = "A déterminer"
                OR
                  (compta.attachment_required = 1 AND compta.attachment_filename IS NULL)
            ) ';
        }

        $sql .= 'ORDER BY compta.date_ecriture, numero_operation';

        $params = [
            'startDate' => $period->dateDebut->format('Y-m-d'),
            'endDate' => $period->dateFin->format('Y-m-d'),
        ];
        if ($filtre !== '') {
            $params['operationType'] = $operationType;
        }

        return $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAllAssociative();
    }
}
