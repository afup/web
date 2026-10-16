<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\InvoicingPeriod;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<InvoicingPeriod>
 */
final class InvoicingPeriodRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoicingPeriod::class);
    }

    public function getCurrentPeriod(?int $periodId = null): InvoicingPeriod
    {
        if ($periodId !== null) {
            return $this->find($periodId);
        }

        $startDate = new \DateTime(date('Y') . '-01-01');
        $endDate = new \DateTime(date('Y') . '-12-31');
        $period = $this->findOneBy([
            'dateDebut' => $startDate,
            'dateFin' => $endDate,
        ]);

        if (!$period instanceof InvoicingPeriod) {
            $period = new InvoicingPeriod();
            $period->dateDebut = $startDate;
            $period->dateFin = $endDate;
            $this->save($period);
        }

        return $period;
    }
}
