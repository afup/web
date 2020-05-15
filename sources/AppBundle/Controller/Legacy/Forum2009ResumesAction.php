<?php

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009ResumesAction
{
    /** @var AppelConferencier */
    private $appelConferencier;
    /** @var Environment */
    private $twig;

    public function __construct(AppelConferencier $appelConferencier, Environment $twig)
    {
        $this->appelConferencier = $appelConferencier;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $sessions = $this->appelConferencier->obtenirListeSessionsAvecResumes(Forum2009Config::ID);

        foreach ($sessions as $index => $session) {
            $sessions[$index]['conferenciers'] = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            $sessions[$index]['journees'] = explode(" ", $session['journee']);
        }

        return new Response($this->twig->render('legacy/forumphp2009/resumes.html.twig', [
            'sessions' => $sessions,
        ]));
    }
}
