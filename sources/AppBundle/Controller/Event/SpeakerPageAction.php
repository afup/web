<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;

class SpeakerPageAction
{
    public function __construct(
        private readonly SpeakerPage $speakerPage,
        private readonly SpeakerFactory $speakerFactory,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $speaker = $this->speakerFactory->getSpeaker($event);

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
