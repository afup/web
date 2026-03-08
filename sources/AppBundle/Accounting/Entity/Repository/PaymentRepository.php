<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Payment;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Payment>
 */
final class PaymentRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @return array<Payment>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.name', 'asc')
            ->getQuery()
            ->execute();
    }
}
