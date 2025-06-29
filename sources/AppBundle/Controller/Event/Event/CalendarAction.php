<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Controller\Event\EventActionHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CalendarAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
    ) {}

    public function __invoke(string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        return $this->render('event/calendar.html.twig', ['event' => $event]);
    }

}
