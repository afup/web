<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\Request;

class SpeakerInfosAction
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakerPage $speakerPage,
    ) {
    }

    public function __invoke(Request $request)
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'), false);
        $speaker = $this->speakerRepository->get($request->get('speaker_id'));

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
