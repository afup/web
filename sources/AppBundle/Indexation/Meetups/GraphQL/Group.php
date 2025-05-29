<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\GraphQL;

final readonly class Group
{
    public function __construct(
        public Events $upcomingEvents,
        public Events $pastEvents,
    ) {}
}
