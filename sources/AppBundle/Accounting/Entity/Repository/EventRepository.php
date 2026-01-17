<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Entity\Repository;

use AppBundle\Accounting\Entity\Event;
use AppBundle\Doctrine\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Event>
 */
final class EventRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return array<Event>
     */
    public function getAllSortedByName(): array
    {
        return $this->createQueryBuilder('e')
                    ->orderBy('e.name', 'asc')
                    ->getQuery()
                    ->execute();
    }
}
