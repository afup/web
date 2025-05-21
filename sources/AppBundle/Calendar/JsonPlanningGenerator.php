<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;

class JsonPlanningGenerator
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PhotoStorage $photoStorage,
    ) {}

    /**
     * @return array<array<string, mixed>>
     */
    public function generate(Event $event): array
    {
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);

        $data = [];

        foreach ($talkAggregates as $talkAggregate) {
            $conferenciers = [];
            foreach ($talkAggregate->speakers as $speaker) {
                $conferenciers[] = [
                    'img' => 'https://afup.org' . $this->photoStorage->getUrl($speaker),
                    'link' => sprintf('https://event.afup.org/%s/speakers/#%d', $event->getPath(), $speaker->getId()),
                    'name' => $speaker->getLabel(),
                ];
            }

            $timeZone = new \DateTimeZone("Europe/Paris");
            $start = $talkAggregate->planning->getStart()->setTimezone($timeZone);
            $end = $talkAggregate->planning->getEnd()->setTimezone($timeZone);

            $data[] = [
                'conferenciers' => $conferenciers,
                'date' => $start->format('d/m/Y H:i') . '-' . $end->format('H:i'),
                'date_start' => $start->format(\Datetime::ISO8601),
                'date_end' => $end->format(\Datetime::ISO8601),
                'detail' => strip_tags(html_entity_decode($talkAggregate->talk->getAbstract())),
                'horaire' => $start->format('H:i') . '-' . $end->format('H:i'),
                'id' => $talkAggregate->talk->getId(),
                'lang' => $talkAggregate->talk->getLanguageLabel(),
                'name' => $talkAggregate->talk->getTitle(),
                'salle' => $talkAggregate->room->getName(),
            ];
        }

        return $data;
    }
}
