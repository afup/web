<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009ResumesAction
{
    public function __construct(
        private readonly AppelConferencier $appelConferencier,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sessions = $this->appelConferencier->obtenirListeSessionsAvecResumes(Forum2009Config::ID);

        foreach ($sessions as $index => $session) {
            $sessions[$index]['conferenciers'] = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            $sessions[$index]['journees'] = explode(" ", (string) $session['journee']);
        }

        return new Response($this->twig->render('legacy/forumphp2009/resumes.html.twig', [
            'sessions' => $sessions,
        ]));
    }
}
