<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Speaker;

use AppBundle\CFP\SpeakerFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $speaker = $this->speakerFactory->findSpeaker($event)
            ?? throw new NotFoundHttpException('Aucun profil conférencier pour cet événement');

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
