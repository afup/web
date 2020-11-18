<?php

namespace App\RendezVous\Admin\ListAttendees;

use App\Action;
use App\RendezVous\RendezVousAttendeeRepository;
use App\RendezVous\RendezVousRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListAttendeesAction implements Action
{
    /** @var RendezVousRepository */
    private $rendezVousRepository;
    /** @var RendezVousAttendeeRepository */
    private $rendezVousAttendeeRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        RendezVousRepository $rendezVousRepository,
        RendezVousAttendeeRepository $rendezVousAttendeeRepository,
        Environment $twig
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->rendezVousAttendeeRepository = $rendezVousAttendeeRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $id = $request->query->getInt('id');
        $rendezVous = $id ? $this->rendezVousRepository->find($id) : $this->rendezVousRepository->findNext();
        Assertion::notNull($rendezVous);

        return new Response($this->twig->render('admin/rendezvous/listing.html.twig', [
            'rendezVousList' => $this->rendezVousRepository->findAll(),
            'rendezVous' => $rendezVous,
            'attendees' => $this->rendezVousAttendeeRepository->findByRendezVous($rendezVous),
        ]));
    }
}
