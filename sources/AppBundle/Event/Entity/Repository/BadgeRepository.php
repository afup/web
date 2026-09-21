<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Badge;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Badge>
 */
final class BadgeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }
}
