<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(): Response
    {
        $events = $this->eventRepository->getNextPublicizedEvents();

        if (count($events) === 0) {
            return $this->render('event/none.html.twig');
        }

        if (count($events) === 1) {
            $event = array_pop($events);
            return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->render('event/switch.html.twig', ['events' => $events]);
    }
}
