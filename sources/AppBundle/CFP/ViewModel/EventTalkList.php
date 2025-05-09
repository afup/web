<?php

declare(strict_types=1);

namespace AppBundle\CFP\ViewModel;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Talk;

class EventTalkList
{
    /** @var Talk[] */
    private array $talks = [];

    public function __construct(
        private readonly Event $event,
        Talk ...$talks,
    ) {
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
