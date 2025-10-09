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

        // le calendrier n'est pas utilisable sur mobile, on redirige vers celui d'event.
        // on ne peux pas le faire de façon automatique, c'est le Forum et je commite depuis la bagagerie,
        // on fait un hotfix moche mais fonctionnel et verra plus tard comment on gère la correspondance
        return new RedirectResponse("https://event.afup.org/forum-php-2025/planning/");
        return new RedirectResponse($this->generateUrl('event_calendar', ['eventSlug' => $event->getPath()]));
    }
}
