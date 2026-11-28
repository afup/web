<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Speaker\SpeakerPage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerInfosAction
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SpeakerRepository $speakerRepository,
        private readonly SpeakerPage $speakerPage,
    ) {}

    public function __invoke(Request $request): RedirectResponse|Response
    {
        $event = $this->eventActionHelper->getFromRequest('id', false)->event;
        $speaker = $this->speakerRepository->find($request->query->getInt('speaker_id'));
        if ($speaker === null) {
            throw new NotFoundHttpException('Speaker non trouvé');
        }

        return $this->speakerPage->handleRequest($request, $event, $speaker);
    }
}
