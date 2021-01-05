<?php

namespace AppBundle\Controller\RendezVous;

use Afup\Site\Rendez_Vous;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class RendezVousArchivesAction
{
    /** @var Rendez_Vous */
    private $rendezVous;
    /** @var Environment */
    private $twig;

    public function __construct(
        Rendez_Vous $rendezVous,
        Environment $twig
    ) {
        $this->rendezVous = $rendezVous;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $rendezVous = $this->rendezVous->obtenirListe();
        if (0 === count($rendezVous)) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }
        foreach ($rendezVous as &$rendezvous) {
            $rendezvous['est_futur'] = $rendezvous['debut'] > time();
            $rendezvous['date'] = date('d/m/Y', $rendezvous['debut']);
            $rendezvous['debut'] = date('H\\hi', $rendezvous['debut']);
            $rendezvous['fin'] = date('H\\hi', $rendezvous['fin']);
        }

        return new Response($this->twig->render('legacy/rendezvous/archives-rendezvous.html.twig', [
            'listerendezvous' => $rendezVous,
        ]));
    }
}
