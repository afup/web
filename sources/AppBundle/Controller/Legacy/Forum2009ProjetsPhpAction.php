<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009ProjetsPhpAction
{
    public function __construct(
        private readonly AppelConferencier $appelConferencier,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $sessions = $this->appelConferencier->obtenirListeProjets(
            Forum2009Config::ID,
            's.*',
            's.titre',
            false,
            false,
            Forum2009Config::PROJECT_IDS
        );

        foreach ($sessions as $index => $session) {
            $sessions[$index]['conferenciers'] = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            $sessions[$index]['journees'] = explode(" ", (string) $session['journee']);
        }

        return new Response($this->twig->render('legacy/forumphp2009/projets-php.html.twig', [
            'projets' => $sessions,
        ]));
    }
}
