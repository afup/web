<?php

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Afup\Site\Forum\Forum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2016PlanningAction
{
    /** @var Forum */
    private $forum;
    /** @var AppelConferencier */
    private $appelConferencier;
    /** @var Environment */
    private $twig;

    public function __construct(
        Forum $forum,
        AppelConferencier $appelConferencier,
        Environment $twig
    ) {
        $this->forum = $forum;
        $this->appelConferencier = $appelConferencier;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $eventId = 15;
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies($eventId);

        return new Response($this->twig->render('legacy/forumphp2016/planning.html.twig', [
            'agenda' => $this->forum->genAgenda(2016, false, false, $eventId, '/forum-php-2016/programme/#$1'),
            'sessions' => $sessions,
        ]));
    }
}
