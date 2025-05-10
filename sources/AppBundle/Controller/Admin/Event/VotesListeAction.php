<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Repository\VoteRepository;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VotesListeAction extends AbstractController implements AdminActionWithEventSelector
{
    public function __construct(
        private readonly RepositoryFactory $repositoryFactory,
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventSelectFactory $eventSelectFactory,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $eventId = $request->query->get('id');
        $event = $this->eventActionHelper->getEventById($eventId);

        $votes = $event === null ? []:$this->repositoryFactory->get(VoteRepository::class)->getVotesByEvent($event->getId());

        return $this->render('admin/vote/liste.html.twig', [
            'votes' => $votes,
            'event' => $event,
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
        ]);
    }
}
