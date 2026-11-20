<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * Une édition qui a au moins un talk publié, avec le nombre de ses talks publiés et de ceux qui ont une vidéo.
 */
final readonly class EventTalksSummary
{
    public function __construct(
        public PublishedEvent $event,
        public int $talkCount,
        public int $videoCount,
    ) {}

    /**
     * L'année de l'édition, celle de sa date de début.
     */
    public function getYear(): int
    {
        return (int) $this->event->dateStart?->format('Y');
    }
}
