<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;

/**
 * Une édition qui a au moins un talk publié, avec le nombre de ses talks publiés et de ceux qui ont une vidéo.
 */
final readonly class EventTalksSummary
{
    public function __construct(
        public Event $event,
        public int $talkCount,
        public int $videoCount,
    ) {}

    /**
     * L'année de l'édition, celle de sa date de début.
     */
    public function getYear(): int
    {
        return (int) $this->event->getDateStart()?->format('Y');
    }

    /**
     * Les éditions regroupées par année, dans l'ordre donné : des éditions triées de la plus récente à la plus
     * ancienne donnent des années dans le même ordre.
     *
     * @param list<self> $summaries
     * @return array<int, non-empty-list<self>>
     */
    public static function groupByYear(array $summaries): array
    {
        $summariesByYear = [];
        foreach ($summaries as $summary) {
            $summariesByYear[$summary->getYear()][] = $summary;
        }

        return $summariesByYear;
    }
}
