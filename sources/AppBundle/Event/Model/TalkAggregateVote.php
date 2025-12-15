<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

final readonly class TalkAggregateVote
{
    public function __construct(
        public float $note,
        public int $total,
    ) {}
}
