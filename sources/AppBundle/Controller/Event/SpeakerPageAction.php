<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;

class SpeakerPageAction
{
    private SpeakerPage $speakerPage;
    private SpeakerFactory $speakerFactory;
    private EventActionHelper $eventActionHelper;

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
