<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Speaker;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PageAction
{
    public function __construct(
        private SpeakerPage $speakerPage,
        private SpeakerFactory $speakerFactory,
        private EventActionHelper $eventActionHelper,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($request->attributes->get('eventSlug'));
        $speaker = $this->speakerFactory->getSpeaker($event);

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
