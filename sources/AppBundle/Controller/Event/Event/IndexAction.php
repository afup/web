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
        // ça y est, après https://github.com/afup/web/pull/1923, plus aucune honte, on commite des redirects comme cela.
        // nos amis de Jolicode vont vouloir nous parler des avantages de redirection.io
        // la page listant les événements ici n'est pas très jolie, il faut que cfp.afup.org redirige vers une page qui est gérée sur event et est bien plus sympa
        // sauf que sur gandi la redirection peux prendre plus d'une demie jourée
        // on fait donc un redirect moche pour le moment et pourra supprimer cela plus tard / quitte à rendre cela plus configurable
        return new RedirectResponse("https://event.afup.org/afup-day-2026/afup-day-2026-appels-a-conferences/");

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
