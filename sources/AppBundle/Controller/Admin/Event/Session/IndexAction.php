<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Session;

use Afup\Site\Forum\Forum;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventSelectFactory $eventSelectFactory,
        private readonly TalkRepository $talkRepository,
        private readonly Forum $forum,
    ) {}

    public function __invoke(Request $request): Response
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'));
        $year = $event->getDateStart()?->format('Y');

        return $this->render('event/session/index.html.twig', [
            'event' => $event,
            'agenda' => $this->forum->genAgenda($year, true, false, $event->getId()),
            'sessions' => $this->talkRepository->getByEventWithSpeakers($event, false),
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }
}
