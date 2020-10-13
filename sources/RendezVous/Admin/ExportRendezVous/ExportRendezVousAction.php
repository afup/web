<?php

namespace App\RendezVous\Admin\ExportRendezVous;

use App\Action;
use App\RendezVous\RendezVousRepository;
use App\RendezVous\RendezVousService;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ExportRendezVousAction implements Action
{
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousService */
    private $rendezVousService;
    /** @var Environment */
    private $twig;

    public function __construct(
        RendezVousService $rendezVousService,
        RendezVousRepository $rendezVousRepository,
        Environment $twig
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousService = $rendezVousService;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $rendezVous = $this->rendezVousRepository->find($id);
        Assertion::notNull($rendezVous);

        return new Response($this->twig->render('admin/rendezvous/export.html.twig', [
            'attendees' => $this->rendezVousService->getBarCampExportList($rendezVous),
        ]));
    }
}
