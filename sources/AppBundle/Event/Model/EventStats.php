<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Model\EventStats\CFPStats;
use AppBundle\Event\Model\EventStats\DailyStats;
use AppBundle\Event\Model\EventStats\TicketTypeStats;

final readonly class EventStats
{
    public function __construct(
        public DailyStats $firstDay,
        public DailyStats $secondDay,
        public TicketTypeStats $ticketType,
        public CFPStats $cfp,
    ) {}
}
