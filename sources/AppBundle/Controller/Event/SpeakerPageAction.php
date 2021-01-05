<?php

namespace AppBundle\Controller\Event;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;

class SpeakerPageAction
{
    /** @var SpeakerPage */
    private $speakerPage;
    /** @var SpeakerFactory */
    private $speakerFactory;
    /** @var EventActionHelper */
    private $eventActionHelper;

    public function __construct(
        SpeakerPage $speakerPage,
        SpeakerFactory $speakerFactory,
        EventActionHelper $eventActionHelper
    ) {
        $this->speakerPage = $speakerPage;
        $this->speakerFactory = $speakerFactory;
        $this->eventActionHelper = $eventActionHelper;
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $speaker = $this->speakerFactory->getSpeaker($event);

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
