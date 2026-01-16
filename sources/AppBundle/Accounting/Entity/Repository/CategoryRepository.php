<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Category;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Category>
 */
final class CategoryRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return array<Category>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'asc')
            ->getQuery()
            ->execute();
    }
}
