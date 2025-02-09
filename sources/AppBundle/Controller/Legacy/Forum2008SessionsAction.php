<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2008SessionsAction
{
    private AppelConferencier $appelConferencier;
    private Environment $twig;

    public function __construct(AppelConferencier $appelConferencier, Environment $twig)
    {
        $this->appelConferencier = $appelConferencier;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies(3);

        foreach ($sessions as $index => $session) {
            $sessions[$index]['conferenciers'] = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            $sessions[$index]['journees'] = explode(" ", $session['journee']);
        }

        return new Response($this->twig->render('legacy/forumphp2008/sessions.html.twig', [
            'sessions' => $sessions,
        ]));
    }
}
