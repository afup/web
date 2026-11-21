<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

/**
 * Un talk publié, vu depuis l'un de ses speakers (une ligne `afup_conferenciers` d'une édition).
 */
final readonly class SpeakerTalk
{
    public function __construct(
        public Speaker $speaker,
        public Talk $talk,
        public Event $event,
        public \DateTimeImmutable $startsAt,
        public ?string $githubLogin = null,
    ) {}
}
