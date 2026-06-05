<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Veille\Entity\Envoi;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Envoi>
 */
class EnvoiRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envoi::class);
    }

    /**
     * @return list<Envoi>
     */
    public function getAllOrderedByDateDesc(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return list<Envoi>
     */
    public function getAllPreviouslySent(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.envoyeMailchimp = true')
            ->andWhere('e.dateEnvoi < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
