<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Talk;
use Sabre\VObject\Component\VCalendar;

class IcsPLanningGenerator
{
    public function __construct(private readonly TalkRepository $talkRepository)
    {
    }

    /**
     * @return string
     */
    public function generateForEvent(Event $event)
    {
        $talks = $this->talkRepository->getByEventWithSpeakers($event);

        $events = [];

        $vcalendar = new VCalendar($events);
        $vcalendar->add('X-WR-CALNAME', $event->getTitle());
        foreach ($talks as $talkWithData) {
            /**
             * @var Talk $talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var Planning $planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var Room $room
             */
            $room = $talkWithData['room'];

            $vcalendar->add('VEVENT', [
                'SUMMARY' => $talk->getTitle(),
                'DTSTART' => $planning->getStart(),
                'DTEND'   => $planning->getEnd(),
                'DESCRIPTION' => strip_tags(html_entity_decode($talk->getAbstract())),
                'LOCATION' => $room->getName(),
            ]);
        }

        return $vcalendar->serialize();
    }
}
