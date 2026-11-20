<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Talk\PublishedEvent;
use AppBundle\Event\Talk\PublishedTalk;

/**
 * Un talk publié, vu depuis l'un de ses speakers (une ligne `afup_conferenciers` d'une édition).
 */
final readonly class SpeakerTalk
{
    public function __construct(
        public Speaker $speaker,
        public PublishedTalk $talk,
        public PublishedEvent $event,
        public \DateTimeImmutable $startsAt,
        public ?string $githubLogin = null,
    ) {}
}
