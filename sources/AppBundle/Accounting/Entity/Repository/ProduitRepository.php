<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Produit;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Produit>
 */
final class ProduitRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @return array<Produit>
     */
    public function getAllSortedByReference(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.reference', 'asc')
            ->getQuery()
            ->execute();
    }
}
