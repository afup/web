<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Site\Entity\Rubrique;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Rubrique>
 */
final class RubriqueRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rubrique::class);
    }

    /**
     * @return Rubrique[]
     */
    public function getAllRubriques(string $ordre = 'nom', string $direction = 'asc', string $filtre = ''): array
    {
        $allowedColumns = ['nom', 'date', 'etat', 'id', 'position'];

        if ($direction !== 'desc' && $direction !== 'asc') {
            $direction = 'asc';
        }

        if (!in_array($ordre, $allowedColumns, true)) {
            $ordre = 'nom';
        }

        return $this->createQueryBuilder('r')
            ->where('r.nom LIKE :filtre')
            ->setParameter('filtre', '%' . $filtre . '%')
            ->orderBy('r.' . $ordre, $direction)
            ->getQuery()
            ->getResult();
    }
}
