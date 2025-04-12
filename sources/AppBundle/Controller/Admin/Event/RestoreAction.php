<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RestoreAction extends AbstractController
{
    private EventRepository $eventRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(EventRepository $eventRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->eventRepository = $eventRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $event = $this->eventRepository->get($id);

        if (!$event instanceof Event) {
            $this->addFlash('error', 'Évènement non trouvé');

            return new RedirectResponse($this->urlGenerator->generate('admin_event_list'));
        }

        $event->setArchivedAt(null);
        $this->eventRepository->save($event);

        return new RedirectResponse($this->urlGenerator->generate('admin_event_edit', ['id' => $id]));
    }
}
