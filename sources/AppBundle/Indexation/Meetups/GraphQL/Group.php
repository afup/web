<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

/**
 * @readonly
 */
final class Group
{
    public function __construct(
        public Events $upcomingEvents,
        public Events $pastEvents,
    ) {
    }
}
