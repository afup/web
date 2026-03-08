<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Site\Entity\Country;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Country>
 */
final class CountryRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @return array<Country>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'asc')
            ->getQuery()
            ->execute();
    }
}
