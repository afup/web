<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity\Repository;

use AppBundle\AssembleeGenerale\Entity\Presence;
use AppBundle\Association\Model\User;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Presence>
 */
class PresenceRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    /**
     * @return Presence[]
     */
    public function getByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.utilisateur = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }
}
