<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\UserBadge;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<UserBadge>
 */
final class UserBadgeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBadge::class);
    }

    /**
     * @return list<UserBadge>
     */
    public function findByUserId(int $userId): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('userBadge', 'badge')
            ->from(UserBadge::class, 'userBadge')
            ->innerJoin('userBadge.badge', 'badge')
            ->where('userBadge.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('userBadge.issuedAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
