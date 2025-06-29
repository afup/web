<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class CalendarLatestAction extends AbstractController
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {}

    public function __invoke(): RedirectResponse
    {
        $event = $this->eventRepository->getCurrentEvent();

        return new RedirectResponse($this->generateUrl('event_calendar', ['eventSlug' => $event->getPath()]));
    }
}
