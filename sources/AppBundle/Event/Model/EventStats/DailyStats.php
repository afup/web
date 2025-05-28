<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

final readonly class DailyStats
{
    public function __construct(
        public int $registered,
        public int $confirmed,
        public int $pending,
    ) {}
}
