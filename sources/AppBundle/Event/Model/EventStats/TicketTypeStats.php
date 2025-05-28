<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

final readonly class TicketTypeStats
{
    public function __construct(
        /** @var list<int> */
        public array $confirmed,

        /** @var list<int> */
        public array $registered,

        /** @var list<int> */
        public array $paying,
    ) {}
}
