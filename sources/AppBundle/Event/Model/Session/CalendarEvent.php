<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Session;

final readonly class CalendarEvent
{
    public function __construct(
        public int $id,
        public string $title,
        public string $start,
        public string $end,
        public int $resourceId,
    ) {}
}
