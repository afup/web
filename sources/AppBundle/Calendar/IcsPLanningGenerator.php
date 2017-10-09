<?php

namespace AppBundle\Calendar;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Talk;
use Sabre\VObject\Component\VCalendar;

class IcsPLanningGenerator
{
    /**
     * @var TalkRepository
     */
    private $talkRepository;

    public function __construct(TalkRepository $talkRepository)
    {
        $this->talkRepository = $talkRepository;
    }

    /**
     * @param Event $event
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
             * @var $talk Talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var $planning Planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var $room Room
             */
            $room = $talkWithData['room'];

            $vcalendar->add('VEVENT', [
                'SUMMARY' => $talk->getTitle(),
                'DTSTART' => $planning->getStart(),
                'DTEND'   => $planning->getEnd(),
                'DESCRIPTION' => $talk->getAbstract(),
                'LOCATION' => $room->getName(),
            ]);
        }

        return $vcalendar->serialize();
    }
}
