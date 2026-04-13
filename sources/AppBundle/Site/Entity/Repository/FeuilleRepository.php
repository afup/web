<?php

declare(strict_types=1);

namespace AppBundle\Site\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Site\Entity\Feuille;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Feuille>
 */
final class FeuilleRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feuille::class);
    }

    /**
     * @return Feuille[]
     */
    public function getAllFeuilles(string $ordre = 'date', string $direction = 'desc', string $filtre = ''): array
    {
        if ($direction !== 'desc' && $direction !== 'asc') {
            $direction = 'asc';
        }

        if (!in_array($ordre, ['nom', 'date', 'etat', 'id', 'position'], true)) {
            $ordre = 'nom';
        }

        if ($ordre === 'date') {
            $ordre = 'dateCreation';
        }

        return $this->createQueryBuilder('f')
            ->where('f.nom LIKE :filtre')
            ->setParameter('filtre', '%' . $filtre . '%')
            ->orderBy('f.' . $ordre, $direction)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Feuille[]
     */
    public function getFeuillesEnfant(int $parentId): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.idParent = :parentId')
            ->andWhere('f.etat = 1')
            ->andWhere('(f.dateDebutPublication IS NULL OR f.dateDebutPublication <= UNIX_TIMESTAMP())')
            ->andWhere('(f.dateFinPublication IS NULL OR f.dateFinPublication >= UNIX_TIMESTAMP())')
            ->setParameter('parentId', $parentId)
            ->getQuery()
            ->getResult();
    }
}
