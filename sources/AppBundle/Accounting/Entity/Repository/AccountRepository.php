<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Account;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Account>
 */
final class AccountRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return array<Account>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'asc')
            ->getQuery()
            ->execute();
    }
}
