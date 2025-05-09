<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2009ConferenciersAction
{
    public function __construct(
        private readonly AppelConferencier $appelConferencier,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $type = $request->query->get('type', 'session');
        if ('projet' === $type) {
            $sessions = $this->appelConferencier->obtenirListeProjets(
                Forum2009Config::ID,
                's.*',
                's.titre',
                false,
                false,
                Forum2009Config::PROJECT_IDS
            );
        } else {
            $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies(Forum2009Config::ID);
        }
        $conferenciers = [];
        foreach ($sessions as $session) {
            if ($session['abstract']) {
                $tmp_conferenciers = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
                foreach ($tmp_conferenciers as $conferencier) {
                    if (!isset($conferenciers[$conferencier['conferencier_id']])) {
                        $conferenciers[$conferencier['conferencier_id']] = $conferencier;
                    }
                    $conferenciers[$conferencier['conferencier_id']]['sessions'][] = [
                        'id' => $session['session_id'],
                        'titre' => $session['titre'],
                    ];
                }
            }
        }

        return new Response($this->twig->render('legacy/forumphp2009/conferenciers.html.twig', [
            'conferenciers' => $conferenciers,
        ]));
    }
}
