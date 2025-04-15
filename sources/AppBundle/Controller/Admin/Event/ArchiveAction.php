<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class ArchiveAction extends AbstractController
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $event = $this->eventRepository->get($id);

        if (!$event instanceof Event) {
            $this->addFlash('error', 'Évènement non trouvé');

            return $this->redirectToRoute('admin_event_list');
        }

        $event->setArchivedAt(new \DateTime());
        $this->eventRepository->save($event);

        return $this->redirectToRoute('admin_event_edit', ['id' => $id]);
    }
}
