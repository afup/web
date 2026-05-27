<?php

declare(strict_types=1);

namespace AppBundle\Event\Form\Support;

use AppBundle\Event\Model\Event;

final class EventHelper
{
    public function groupByYear(string|Event $event): string
    {
        if ($event instanceof Event) {
            $start = $event->getDateStart();

            if ($start !== null) {
                return 'Année ' . $start->format('Y');
            }

            $title = $event->getTitle();
        } else {
            $title = $event;
        }

        if ($title !== null && preg_match('/\d{4}/', $title, $matches)) {
            return 'Année ' . $matches[0];
        }

        return 'Année inconnue';
    }

    /**
     * @param array<Event> $events
     * @return array<Event>
     */
    public function sortEventsByStartDate(array $events): array
    {
        usort($events, static fn(Event $a, Event $b): int => $b->getDateStart() <=> $a->getDateStart());

        return $events;
    }
}
