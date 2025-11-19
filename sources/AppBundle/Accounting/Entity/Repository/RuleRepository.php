<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Rule;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Rule>
 */
final class RuleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    /**
     * @return array<Rule>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.label', 'asc')
            ->getQuery()
            ->execute();
    }
}
