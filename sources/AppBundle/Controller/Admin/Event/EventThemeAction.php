<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventThemeRepository;
use CCMBenchmark\TingBundle\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventThemeAction extends AbstractController
{
    public function __construct(private readonly EventThemeRepository $eventThemeRepository, private readonly EventRepository $eventRepository, private readonly EventSelectFactory $eventSelectFactory) {}

    public function __invoke(Request $request, #[MapEntity(id: 'id')] ?Event $event = null): Response
    {
        if ($request->query->has('id')) {
            return $this->redirectToRoute('admin_event_themes_list', ['id' => $request->query->getInt('id')]);
        }
        if ($event === null) {
            $event = $this->eventRepository->getLastEvent();
        }
        $list = $this->eventThemeRepository->getBy(['idForum' => $event->getId()]);
        $cloneReq = clone $request;
        $cloneReq->query->set('id', $event->getId());
        return $this->render('admin/event/theme_list.html.twig', [
            'themes' => $list,
            'event' => $event,
            'event_select_form' => $this->eventSelectFactory->create($event, $cloneReq)->createView(),
        ]);
    }
}
