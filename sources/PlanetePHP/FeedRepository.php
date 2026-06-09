<?php

declare(strict_types=1);

namespace PlanetePHP;

use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Feed>
 */
final class FeedRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    /**
     * @return array<Feed>
     */
    public function findActive(): array
    {
        /** @var array<Feed> $feeds */
        $feeds = $this->createQueryBuilder('f')
            ->where('f.status = :status')
            ->setParameter('status', FeedStatus::Active)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $feeds;
    }

    /**
     * @return array<Feed>
     */
    public function findAllOrderedByName(): array
    {
        /** @var array<Feed> $feeds */
        $feeds = $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $feeds;
    }
}
