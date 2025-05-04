<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2016PlanningAction
{
    public function __construct(
        private readonly Forum $forum,
        private readonly AppelConferencier $appelConferencier,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $eventId = 15;
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies($eventId);

        return new Response($this->twig->render('legacy/forumphp2016/planning.html.twig', [
            'agenda' => $this->forum->genAgenda(2016, false, false, $eventId, '/forum-php-2016/programme/#$1'),
            'sessions' => $sessions,
        ]));
    }
}
