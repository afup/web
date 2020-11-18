<?php

namespace App\RendezVous\Admin\ListRendezVous;

use App\Action;
use App\RendezVous\RendezVousAttendeeRepository;
use App\RendezVous\RendezVousRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ListRendezVousAction implements Action
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
        $filter = $request->query->get('filter');
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        $nbComing = $nbPending = 0;
        $attendees = [];
        if (null !== $rendezVous) {
            $nbComing = $this->rendezVousAttendeeRepository->countComing($rendezVous);
            $nbPending = $this->rendezVousAttendeeRepository->countPending($rendezVous);
            $attendees = $this->rendezVousAttendeeRepository->search($rendezVous, $sort, $direction, $filter);
        }

        return new Response($this->twig->render('admin/rendezvous/list.html.twig', [
            'rendezVousList' => $this->rendezVousRepository->findAll(),
            'nbComing' => $nbComing,
            'nbPending' => $nbPending,
            'rendezVous' => $rendezVous,
            'attendees' => $attendees,
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }
}
