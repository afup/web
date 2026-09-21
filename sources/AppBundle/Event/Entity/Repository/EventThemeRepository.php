<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity\Repository;

use AppBundle\Doctrine\EntityRepository;
use AppBundle\Event\Entity\EventTheme;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<EventTheme>
 */
final class EventThemeRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTheme::class);
    }

    /**
     * @return array<EventTheme>
     */
    public function getByThemesOrderedByPriority(int $eventId): array
    {
        return $this->findBy(['idForum' => $eventId], ['priority' => 'ASC', 'name' => 'ASC']);
    }
}
