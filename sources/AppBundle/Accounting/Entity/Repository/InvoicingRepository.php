<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Invoicing;
use AppBundle\Accounting\Entity\InvoicingPeriod;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Invoicing>
 */
final class InvoicingRepository extends EntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly InvoicingPeriodRepository $invoicingPeriodRepository,
    ) {
        parent::__construct($registry, Invoicing::class);
    }

    public function getById(int $id): ?Invoicing
    {
        return $this->find($id);
    }

    public function getOneByInvoiceNumber(string $number): ?Invoicing
    {
        return $this->findOneBy(['numeroFacture' => $number]);
    }

    public function getOneByQuotationNumber(string $number): ?Invoicing
    {
        return $this->findOneBy(['numeroDevis' => $number]);
    }

    /**
     * @return array<Invoicing>
     */
    public function getQuotationsByPeriodId(?int $periodId = null, string $sort = 'date', string $direction = 'desc'): array
    {
        $field = $sort === 'client' ? 'i.societe' : 'i.dateDevis';

        $qb = $this->createQueryBuilder('i')
            ->addSelect('SUM(d.quantite * d.prixUnitaire) as prix')
            ->leftJoin('i.details', 'd')
            ->where("i.numeroDevis != ''")
            ->groupBy('i.id')
            ->orderBy($field, $direction);

        if ($periodId !== null && $periodId !== 0) {
            $period = $this->invoicingPeriodRepository->find($periodId);
            if (!$period instanceof InvoicingPeriod) {
                return [];
            }

            $qb->andWhere('i.dateDevis >= :periodStart')
                ->andWhere('i.dateDevis <= :periodEnd')
                ->setParameter('periodStart', $period->dateDebut)
                ->setParameter('periodEnd', $period->dateFin);
        }

        return $this->mapRowsToInvoicings($qb->getQuery()->getResult());
    }

    /**
     * @return array<Invoicing>
     */
    public function getInvoicesByPeriodId(?int $periodId = null, string $sort = 'date', string $direction = 'desc'): array
    {
        $field = $sort === 'client' ? 'i.societe' : 'i.dateFacture';

        $qb = $this->createQueryBuilder('i')
            ->addSelect('SUM(d.quantite * d.prixUnitaire) as prix')
            ->leftJoin('i.details', 'd')
            ->where("i.numeroFacture != ''")
            ->groupBy('i.id')
            ->orderBy($field, $direction);

        if ($periodId !== null && $periodId !== 0) {
            $period = $this->invoicingPeriodRepository->find($periodId);
            if (!$period instanceof InvoicingPeriod) {
                return [];
            }

            $qb->andWhere('i.dateFacture >= :periodStart')
                ->andWhere('i.dateFacture <= :periodEnd')
                ->setParameter('periodStart', $period->dateDebut)
                ->setParameter('periodEnd', $period->dateFin);
        }

        return $this->mapRowsToInvoicings($qb->getQuery()->getResult());
    }

    public function getNextInvoiceIndex(int $year): ?int
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery(
            "SELECT MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 AS next_index FROM afup_compta_facture WHERE LEFT(numero_facture, 4) = :year",
            ['year' => (string) $year],
        )->fetchOne();

        return is_numeric($result) ? (int) $result : null;
    }

    public function getNextQuotationIndex(int $year): ?int
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery(
            "SELECT MAX(CAST(SUBSTRING_INDEX(numero_devis, '-', -1) AS UNSIGNED)) + 1 AS next_index FROM afup_compta_facture WHERE LEFT(numero_devis, 4) = :year",
            ['year' => (string) $year],
        )->fetchOne();

        return is_numeric($result) ? (int) $result : null;
    }

    public function convertQuotationToInvoice(Invoicing $quotation, string $invoiceNumber): void
    {
        $quotation->numeroFacture = $invoiceNumber;
        $quotation->dateFacture = new \DateTime();
        $this->save($quotation);
    }

    /**
     * Recherche comptable : devis et factures dont un champ texte contient la recherche,
     * avec le total et les références/désignations des lignes (quantité positive).
     *
     * @return list<array<string, mixed>>
     */
    public function searchAllQuotesAndInvoices(string $search): array
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();

        return $queryBuilder
            ->select(
                'inv.*',
                'SUM(det.pu * det.quantite) AS total',
                "GROUP_CONCAT(det.ref SEPARATOR ', ') AS refs",
                "GROUP_CONCAT(det.designation SEPARATOR ', ') AS details",
            )
            ->from('afup_compta_facture', 'inv')
            ->leftJoin('inv', 'afup_compta_facture_details', 'det', 'det.idafup_compta_facture = inv.id AND det.quantite > 0')
            ->where($queryBuilder->expr()->or(
                'inv.numero_devis LIKE :like',
                'inv.numero_facture LIKE :like',
                'inv.societe LIKE :like',
                'inv.service LIKE :like',
                'inv.email LIKE :like',
                'inv.ref_clt1 LIKE :like',
                'inv.ref_clt2 LIKE :like',
                'inv.ref_clt3 LIKE :like',
                'inv.observation LIKE :like',
            ))
            ->groupBy('inv.id')
            ->setParameter('like', '%' . $search . '%')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param array<array{0: Invoicing, prix: float|null}> $rows
     * @return array<Invoicing>
     */
    private function mapRowsToInvoicings(array $rows): array
    {
        return array_map(static function (array $row): Invoicing {
            $row[0]->prix = (float) $row['prix'];

            return $row[0];
        }, $rows);
    }
}
