<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VotesListeAction extends AbstractController
{
    public function __construct(
        private readonly VoteRepository $voteRepository,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $votes = $this->voteRepository->getVotesByEvent($event->getId());

        return $this->render('admin/vote/liste.html.twig', [
            'votes' => $votes,
            'event' => $event,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
