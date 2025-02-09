<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Group
{
    public Events $upcomingEvents;
    public Events $pastEvents;

    public function __construct(Events $upcomingEvents, Events $pastEvents)
    {
        $this->upcomingEvents = $upcomingEvents;
        $this->pastEvents = $pastEvents;
    }
}
