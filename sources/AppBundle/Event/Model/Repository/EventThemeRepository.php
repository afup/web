<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\EventTheme;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Repository\CollectionInterface;

/**
 * @extends Repository<EventTheme>
 */
class EventThemeRepository extends Repository
{
    /**
     * @return CollectionInterface<EventTheme>
     */
    public function getByEventOrderedByPriority(int $eventId): CollectionInterface
    {
        return $this->getBy(['idForum' => $eventId], ['priority' => 'ASC', 'name' => 'ASC']);
    }
}
