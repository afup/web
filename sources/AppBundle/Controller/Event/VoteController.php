<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Event\Form\VoteType;
use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Notifier\SlackNotifier;
use CCMBenchmark\Ting\Exception;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class VoteController extends AbstractController
{
    private ?FormBuilderInterface $formBuilder = null;
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RepositoryFactory $repositoryFactory,
        private readonly SlackNotifier $slackNotifier,
        private readonly EventActionHelper $eventActionHelper,
    ) {}

    /**
     * @param bool $all if true => show all talks to rate even if already rated by the current user
     */
    public function index(string $eventSlug, int $page = 1, bool $all = false): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        if (!$event->isVoteAvailable()) {
            return $this->render('event/cfp/closed.html.twig', ['event' => $event]);
        }

        $talkRepository = $this->repositoryFactory->get(TalkRepository::class);

        // Get a random list of unrated talks
        if ($all === false) {
            $talks = $talkRepository->getNewTalksToRate($event, $this->getUser(), crc32($this->requestStack->getSession()->getId()), $page);
        } else {
            $talks = $talkRepository->getAllTalksAndRatingsForUser($event, $this->getUser(), crc32($this->requestStack->getSession()->getId()), $page);
        }

        $vote = new Vote();
        $forms = function () use ($talks, $vote, $eventSlug) {
            foreach ($talks as $session) {
                /** @var Talk $talk */
                $talk = $session['sessions'];
                $myVote = $session['asvg'] ?? clone $vote;
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

    private function createVoteForm(string $eventSlug, int $talkId, Vote $vote): FormInterface
    {
        if (!$this->formBuilder instanceof FormBuilderInterface) {
            $this->formBuilder = $this->createFormBuilder();
        }

        $vote->setSessionId($talkId);

        return $this
            ->formBuilder->create(
                'vote' . $talkId,
                VoteType::class,
                ['data' => $vote],
            )->setAction(
                $this->generateUrl('vote_new', ['talkId' => $talkId, 'eventSlug' => $eventSlug]),
            )
            ->setMethod(Request::METHOD_POST)
            ->getForm();
    }

    public function new(Request $request, string $eventSlug, int $talkId): JsonResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        if (!$event->isVoteAvailable()) {
            return new JsonResponse(['errors' => ['Cfp is closed !']], Response::HTTP_BAD_REQUEST);
        }

        $vote = new Vote();
        if ($this->getUser() instanceof GithubUser) {
            $vote->setUser($this->getUser()->getId());
        }

        $form = $this->createVoteForm($eventSlug, $talkId, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() === false) {
            return new JsonResponse(['errors' => ['Form not submitted']], Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid() === false) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $talk = $this->findTalk($talkId);
        if (!$talk) {
            return new JsonResponse(['errors' => ['Talk does not exists']], Response::HTTP_BAD_REQUEST);
        }

        $voteRepository = $this->repositoryFactory->get(VoteRepository::class);
        $vote = $form->getData();
        $vote->setSubmittedOn(new \DateTime());

        try {
            $vote->setTalk($talk);
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($vote): void {
                $this->slackNotifier->notifyVote($vote);
            });
            $voteRepository->upsert($vote);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['errors' => []]);
    }

    private function findTalk(int $talkId): Talk|false
    {
        $talkRepository = $this->repositoryFactory->get(TalkRepository::class);
        /** @var Talk $talk */
        $talk = $talkRepository->getOneBy(['id' => $talkId]);
        if ($talk) {
            return $talk;
        }

        return false;
    }
}
