<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

final readonly class CFPStats
{
    public function __construct(
        public int $talks,
        public int $speakers,
    ) {}
}
