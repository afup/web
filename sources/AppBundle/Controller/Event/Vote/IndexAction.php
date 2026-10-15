<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Vote;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\Repository\VoteRepository;
use AppBundle\Event\Entity\Vote;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Security\Authentication;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends VoteController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
        private readonly VoteRepository $voteRepository,
        private readonly Authentication $authentication,
    ) {}

    /**
     * @param bool $all if true => show all talks to rate even if already rated by the current user
     */
    public function __invoke(string $eventSlug, int $page = 1, bool $all = false): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        if (!$event->isVoteAvailable()) {
            return $this->render('event/cfp/closed.html.twig', ['event' => $event]);
        }

        // Get a random list of unrated talks
        $githubUser = $this->authentication->getGithubUser();
        if ($all === false) {
            $talks = $this->talkRepository->getNewTalksToRate($event, $githubUser, crc32($this->requestStack->getSession()->getId()), $page);
            $myUserVotes = [];
        } else {
            $talks = $this->talkRepository->getAllTalksAndRatingsForUser($event, $githubUser, crc32($this->requestStack->getSession()->getId()), $page);
            $myUserVotes = $this->voteRepository->getUserVotesByEvent((int) $githubUser->getId(), (int) $event->getId());
        }

        $vote = new Vote();
        $forms = function () use ($talks, $vote, $myUserVotes, $eventSlug) {
            foreach ($talks as $session) {
                /** @var Talk $talk */
                $talk = $session['sessions'];
                $myVote = $myUserVotes[(int) $talk->getId()] ?? clone $vote;
                /*
                 * By using a yield here, there will be only one iteration over the talks for the entire page
                 */
                yield [
                    'form' => $this->createVoteForm($eventSlug, $talk->getId(), $myVote)->createView(),
                    'talk' => $talk,
                ];
            }
        };

        return $this->render('event/vote/liste.html.twig', [
            'numberOfTalks' => $talks->count(),
            'route' => ($all === true ? 'vote_all_paginated' : 'vote_index_paginated'),
            'page' => $page,
            'talks' => $forms(),
            'event' => $event,
            'all' => $all,
        ]);
    }
}
