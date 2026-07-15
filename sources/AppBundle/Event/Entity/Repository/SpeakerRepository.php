<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\Speaker;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Speaker>
 */
final class SpeakerRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Speaker::class);
    }
}
