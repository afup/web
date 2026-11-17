<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

/**
 * Un talk publié d'une édition, avec son créneau et ses speakers (les lignes speaker de cette édition).
 */
final readonly class EventTalk
{
    /**
     * @param list<Speaker> $speakers
     */
    public function __construct(
        public Talk $talk,
        public \DateTimeImmutable $startsAt,
        public array $speakers,
    ) {}
}
