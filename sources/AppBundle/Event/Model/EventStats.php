<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Model\EventStats\DailyStats;
use AppBundle\Event\Model\EventStats\TicketTypeStats;

class EventStats
{
    /**
     * @var DailyStats
     */
    public $firstDay;
    /**
     * @var DailyStats
     */
    public $secondDay;
    /**
     * @var TicketTypeStats
     */
    public $ticketType;

    public function __construct()
    {
        $this->firstDay = new DailyStats();
        $this->secondDay = new DailyStats();
        $this->ticketType = new TicketTypeStats();
    }
}
