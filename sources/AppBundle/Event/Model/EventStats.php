<?php

namespace AppBundle\Event\Model;

use AppBundle\Event\Model\EventStats\DailyStats;
use AppBundle\Event\Model\EventStats\TicketTypeStats;

class EventStats
{
    public $firstDay;
    public $secondDay;
    public $ticketType;

    public function __construct()
    {
        $this->firstDay = new DailyStats();
        $this->secondDay = new DailyStats();
        $this->ticketType = new TicketTypeStats();
    }
}
