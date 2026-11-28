<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\Planning;
use AppBundle\Event\Entity\Room;
use AppBundle\Event\Entity\Speaker;

final readonly class TalkAggregate
{
    public function __construct(
        public Talk $talk,
        /** @var array<Speaker> $speakers */
        public array $speakers,
        public ?Room $room,
        public ?Planning $planning,
        public ?TalkAggregateVote $vote = null,
    ) {}
}
