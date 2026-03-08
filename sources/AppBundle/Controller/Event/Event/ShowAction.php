<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ShowAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
        private readonly VoteRepository $voteRepository,
    ) {}

    public function __invoke(string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $talks = $this->talkRepository->getNumberOfTalksByEvent($event);
        $votes = $this->voteRepository->getNumberOfVotesByEvent($event);

        $currentDate = new \DateTime();

        if ($event->getDateEndCallForPapers() < $currentDate) {
            if (!$event->isVoteAvailable()) {
                return $this->render('event/cfp/closed.html.twig', ['event' => $event]);
            }

            return $this->render('event/cfp/vote_only.html.twig', ['event' => $event, 'talks' => $talks['talks'], 'votes' => $votes['votes']]);
        }

        return $this->render('event/home.html.twig', ['event' => $event, 'talks' => $talks['talks'], 'votes' => $votes['votes']]);
    }
}
