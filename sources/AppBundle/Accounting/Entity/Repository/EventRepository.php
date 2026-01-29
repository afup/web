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
    public function getAllSortedByName(bool $usedInAccountingJournal = false): array
    {
        $query = $this->createQueryBuilder('e');
        if ($usedInAccountingJournal) {
            $query->andWhere('e.hideInAccountingJournalAt IS NULL');
        }
        return $query->orderBy('e.name', 'asc')
                    ->getQuery()
                    ->execute();
    }
}
