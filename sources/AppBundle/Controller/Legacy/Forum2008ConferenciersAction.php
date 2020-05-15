<?php

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2008ConferenciersAction
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
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies(3);
        $conferenciers = [];
        foreach ($sessions as $session) {
            foreach ($this->appelConferencier->obtenirConferenciersPourSession($session['session_id']) as $conferencier) {
                if (!isset($conferenciers[$conferencier['conferencier_id']])) {
                    $conferenciers[$conferencier['conferencier_id']] = $conferencier;
                }
                $conferenciers[$conferencier['conferencier_id']]['sessions'][] = [
                    'id' => $session['session_id'],
                    'titre' => $session['titre'],
                ];
            }
        }

        return new Response($this->twig->render('legacy/forumphp2008/conferenciers.html.twig', [
            'conferenciers' => $conferenciers,
        ]));
    }
}
