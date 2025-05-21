<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use Sabre\VObject\Component\VCalendar;

class IcsPLanningGenerator
{
    public function __construct(private readonly TalkRepository $talkRepository) {}

    public function generateForEvent(Event $event): string
    {
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);

        $events = [];

        $vcalendar = new VCalendar($events);
        $vcalendar->add('X-WR-CALNAME', $event->getTitle());
        foreach ($talkAggregates as $talkAggregate) {
            $vcalendar->add('VEVENT', [
                'SUMMARY' => $talkAggregate->talk->getTitle(),
                'DTSTART' => $talkAggregate->planning->getStart(),
                'DTEND'   => $talkAggregate->planning->getEnd(),
                'DESCRIPTION' => strip_tags(html_entity_decode($talkAggregate->talk->getAbstract())),
                'LOCATION' => $talkAggregate->room->getName(),
            ]);
        }

        return $vcalendar->serialize();
    }
}
