<?php

declare(strict_types=1);

namespace AppBundle\Event\Model\EventStats;

class DailyStats
{
    /** @var int */
    public $registered = 0;
    /** @var int */
    public $confirmed = 0;
    /** @var int */
    public $pending = 0;
}
