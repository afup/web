<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\ParametrageSponsoring;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<ParametrageSponsoring>
 */
final class ParametrageSponsoringRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParametrageSponsoring::class);
    }

    public function get(): ParametrageSponsoring
    {
        return $this->find(ParametrageSponsoring::ID) ?? new ParametrageSponsoring();
    }
}
