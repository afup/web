<?php

declare(strict_types=1);

namespace AppBundle\Controller\Legacy;

use Afup\Site\Forum\AppelConferencier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Forum2016ConferenciersAction
{
    public function __construct(
        private readonly AppelConferencier $appelConferencier,
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $eventId = 15;
        $sessions = $this->appelConferencier->obtenirListeSessionsPlannifies($eventId);

        $conferenciers = [];
        foreach ($sessions as $session) {
            $tmpConferenciers = $this->appelConferencier->obtenirConferenciersPourSession($session['session_id']);
            foreach ($tmpConferenciers as $conferencier) {
                if ('À définir' === $conferencier['nom']) {
                    continue;
                }

                if (!isset($conferenciers[$conferencier['conferencier_id']])) {
                    $conferencier['prenom'] = ucfirst(strtolower((string) $conferencier['prenom']));
                    $conferencier['nom'] = strtoupper((string) $conferencier['nom']);
                    $conferenciers[$conferencier['conferencier_id']] = $conferencier;
                }

                $conferenciers[$conferencier['conferencier_id']]['sessions'][] = [
                    'id' => $session['session_id'],
                    'titre' => $session['titre'],
                ];
            }
        }
        uasort($conferenciers, static fn ($a, $b): int => $a['prenom'] <=> $b['prenom']);

        return new Response($this->twig->render('legacy/forumphp2016/conferenciers.html.twig', [
            'conferenciers' => $conferenciers,
        ]));
    }
}
