<?php

namespace AppBundle\Event\Model\EventStats;

class TicketTypeStats
{
    /** @var array<int, int> */
    public $confirmed = [];
    /** @var array<int, int> */
    public $registered = [];
    /** @var array<int, int> */
    public $paying = [];
}
