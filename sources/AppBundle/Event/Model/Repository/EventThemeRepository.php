<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Repository;

use AppBundle\Event\Model\EventTheme;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Repository;

/**
 * @extends Repository<EventTheme>
 */
class EventThemeRepository extends Repository
{
    /**
     * @return CollectionInterface<EventTheme>
     */
    public function getByThemesOrderedByPriority(int $eventId): CollectionInterface
    {
        return $this->getPreparedQuery(
            'SELECT * FROM afup_conference_theme WHERE id_forum = :idForum ORDER BY priority ASC, name ASC',
        )->setParams(['idForum' => $eventId])->query($this->getCollection(new HydratorSingleObject()));
    }
}
