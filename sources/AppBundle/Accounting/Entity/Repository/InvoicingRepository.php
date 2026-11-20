<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Invoicing;
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

        return $result !== null ? (int) $result : null;
    }

    public function getNextQuotationIndex(int $year): ?int
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery(
            "SELECT MAX(CAST(SUBSTRING_INDEX(numero_devis, '-', -1) AS UNSIGNED)) + 1 AS next_index FROM afup_compta_facture WHERE LEFT(numero_devis, 4) = :year",
            ['year' => (string) $year],
        )->fetchOne();

        return $result !== null ? (int) $result : null;
    }

    public function convertQuotationToInvoice(Invoicing $quotation, string $invoiceNumber): void
    {
        $quotation->numeroFacture = $invoiceNumber;
        $quotation->dateFacture = new \DateTime();
        $this->save($quotation);
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
