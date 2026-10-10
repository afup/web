<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
final class ArchiveAction extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository) {}

    public function __invoke(int $id): RedirectResponse
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
