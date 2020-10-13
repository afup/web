<?php

namespace App\RendezVous\ListArchives;

use App\Action;
use App\RendezVous\RendezVousRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListArchivesAction implements Action
{
    /** @var Environment */
    private $twig;
    /** @var RendezVousRepository */
    private $rendezVousRepository;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        Environment $twig
    ) {
        $this->twig = $twig;
        $this->rendezVousRepository = $rendezVousRepository;
    }

    public function __invoke(Request $request)
    {
        $list = [];
        foreach ($this->rendezVousRepository->findAll() as $rendezvous) {
            $list[] = [
                'est_futur' => $rendezvous->getStart() > time(),
                'date' => date('d/m/Y', $rendezvous->getStart()),
                'debut' => date('H\\hi', $rendezvous->getStart()),
                'fin' => date('H\\hi', $rendezvous->getEnd()),
            ];
        }
        if ([] === $list) {
            return new Response($this->twig->render('legacy/rendezvous/pas-de-rendezvous.html.twig'));
        }

        return new Response($this->twig->render('legacy/rendezvous/archives-rendezvous.html.twig', [
            'listerendezvous' => $list,
        ]));
    }
}
