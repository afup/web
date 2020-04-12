<?php

namespace AppBundle\CFP\ViewModel;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;

class EventTalkList
{
    /** @var Event */
    private $event;
    /** @var Talk[] */
    private $talks = [];

    public function __construct(Event $event, Talk ...$talks)
    {
        $this->event = $event;
        foreach ($talks as $talk) {
            $this->addTalk($talk);
        }
    }

    public function addTalk(Talk $talk)
    {
        $this->talks[$talk->getId()] = $talk;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return Talk[]
     */
    public function getTalks()
    {
        return $this->talks;
    }
}
