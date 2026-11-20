<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * Un talk publié d'une édition, avec son créneau et ses speakers (les lignes speaker de cette édition).
 */
final readonly class EventTalk
{
    /**
     * @param list<PublishedSpeaker> $speakers
     */
    public function __construct(
        public PublishedTalk $talk,
        public \DateTimeImmutable $startsAt,
        public array $speakers,
    ) {}
}
