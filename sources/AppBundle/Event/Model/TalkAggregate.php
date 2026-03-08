<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

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
