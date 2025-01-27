<?php

declare(strict_types=1);

namespace AppBundle\CFP\ViewModel;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;

class EventTalkList
{
    private Event $event;
    /** @var Talk[] */
    private array $talks = [];

    public function __construct(Event $event, Talk ...$talks)
    {
        $this->event = $event;
        foreach ($talks as $talk) {
            $this->addTalk($talk);
        }
    }

    public function addTalk(Talk $talk): void
    {
        $this->talks[$talk->getId()] = $talk;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @return Talk[]
     */
    public function getTalks(): array
    {
        return $this->talks;
    }
}
