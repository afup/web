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
        $events = $this->eventRepository->getNextEvents();

        if ($events === null) {
            return $this->render('event/none.html.twig');
        } elseif ($events->count() === 1) {
            $event = $events->first();
            return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->render('event/switch.html.twig', ['events' => $events]);
    }
}
