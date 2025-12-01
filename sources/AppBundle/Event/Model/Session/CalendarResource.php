<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\Session;

final readonly class CalendarResource
{
    public function __construct(
        public int $id,
        public string $title,
        public string $eventBackgroundColor,
    ) {}
}
