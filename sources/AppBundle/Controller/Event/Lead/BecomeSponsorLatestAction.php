<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Lead;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Redirige vers la page de sponsoring du dernier évènement.
 */
final class BecomeSponsorLatestAction extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository) {}

    public function __invoke(): RedirectResponse
    {
        // Entre la fin d'un évènement et la création du suivant, il n'y a pas d'évènement courant
        $event = $this->eventRepository->getCurrentEvent() ?? $this->eventRepository->getLastEvent();

        if (!$event instanceof Event) {
            throw $this->createNotFoundException();
        }

        return new RedirectResponse($this->generateUrl('sponsor_leads', [
            'eventSlug' => $event->getPath(),
        ]));
    }
}
