<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use Sabre\VObject\Component\VCalendar;

class IcsPlanningGenerator
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PlanningRepository $planningRepository,
    ) {}

    public function generateForEvent(Event $event): string
    {
        $talkAggregates = $this->planningRepository->enrichTalkAggregates(
            $this->talkRepository->getByEventWithSpeakers($event),
        );

        $events = [];

        $vcalendar = new VCalendar($events);
        $vcalendar->add('X-WR-CALNAME', $event->getTitle());
        foreach ($talkAggregates as $talkAggregate) {
            if ($talkAggregate->planning === null || $talkAggregate->room === null) {
                continue;
            }

            $vcalendar->add('VEVENT', [
                'SUMMARY' => $talkAggregate->talk->getTitle(),
                'DTSTART' => $talkAggregate->planning->start,
                'DTEND'   => $talkAggregate->planning->end,
                'DESCRIPTION' => strip_tags(html_entity_decode($talkAggregate->talk->getAbstract())),
                'LOCATION' => $talkAggregate->room->getName(),
            ]);
        }

        return $vcalendar->serialize();
    }
}
