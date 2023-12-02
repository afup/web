<?php


namespace AppBundle\Controller;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Form\VoteType;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\Notifier\SlackNotifier;
use CCMBenchmark\Ting;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class VoteController extends EventBaseController
{
    private $formBuilder;

    /**
     * @param $eventSlug
     * @param int $page
     * @param bool $all if true => show all talks to rate even if already rated by the current user
     * @return Response
     */
    public function indexAction($eventSlug, $page = 1, $all = false)
    {
        $event = $this->checkEventSlug($eventSlug);
        if (!$event->isVoteAvailable()) {
            return $this->render(':event:cfp/closed.html.twig', ['event' => $event]);
        }

        /**
         * @var TalkRepository $talkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);

        // Get a random list of unrated talks
        if ($all === false) {
            $talks = $talkRepository->getNewTalksToRate($event, $this->getUser(), crc32($this->get('session')->getId()), $page);
        } else {
            $talks = $talkRepository->getAllTalksAndRatingsForUser($event, $this->getUser(), crc32($this->get('session')->getId()), $page);
        }

        $vote = new Vote();
        $forms = function () use ($talks, $vote, $eventSlug) {
            foreach ($talks as $talk) {
                if (isset($talk['asvg'])) {
                    $myVote = $talk['asvg'];
                } else {
                    $myVote = clone $vote;
                }
                /*
                 * By using a yield here, there will be only one iteration over the talks for the entire page
                 */
                yield [
                    'form' => $this->createVoteForm($eventSlug, $talk['sessions']->getId(), $myVote)->createView(),
                    'talk' => $talk['sessions']
                ];
            }
        };

        return $this->render(
            'event/vote/liste.html.twig',
            [
                'numberOfTalks' => $talks->count(),
                'route' => ($all === true ? 'vote_all_paginated':'vote_index_paginated'),
                'page' => $page,
                'talks' => $forms(),
                'event' => $event,
                'all' => $all
            ]
        );
    }

    /**
     * @param string $eventSlug
     * @param int $talkId
     * @param Vote $vote
     * @return FormInterface
     */
    private function createVoteForm($eventSlug, $talkId, Vote $vote)
    {
        if ($this->formBuilder === null) {
            $this->formBuilder = $this->createFormBuilder();
        }

        $vote->setSessionId($talkId);

        return $this
            ->formBuilder->create(
                'vote' . $talkId,
                VoteType::class,
                ['data' => $vote]
            )->setAction(
                $this->generateUrl('vote_new', ['talkId' => $talkId, 'eventSlug' => $eventSlug])
            )
            ->setMethod('POST')
            ->getForm();
    }

    public function newAction(Request $request, $eventSlug, $talkId)
    {
        $event = $this->checkEventSlug($eventSlug);
        if (!$event->isVoteAvailable()) {
            return new JsonResponse(['errors' => ['Cfp is closed !']], Response::HTTP_BAD_REQUEST);
        }

        $vote = new Vote();
        $vote->setUser($this->getUser()->getId());

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

        /** @var VoteRepository $voteRepository */
        $voteRepository = $this->get('ting')->get(VoteRepository::class);
        /** @var Vote $vote */
        $vote = $form->getData();
        $vote->setSubmittedOn(new \DateTime());

        try {
            $vote->setTalk($talk);
            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($vote) {
                $this->get(SlackNotifier::class)->notifyVote($vote);
            });
            $voteRepository->upsert($vote);
        } catch (Ting\Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['errors' => []]);
    }

    public function adminAction(Request $request)
    {
        $eventId = $request->query->get('id');
        $event = $this->get(EventActionHelper::class)->getEventById($eventId);

        $votes = $event === null ? []:$this->get('ting')->get(VoteRepository::class)->getVotesByEvent($event->getId());

        return $this->render('admin/vote/liste.html.twig', [
            'votes' => $votes,
            'title' => 'Votes',
            'event' => $event,
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }

    /**
     * @param $talkId
     * @return Talk|false
     */
    private function findTalk($talkId)
    {
        /** @var TalkRepository $talkRepository */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        /** @var Talk $talk */
        $talk = $talkRepository->getOneBy(['id' => $talkId]);
        if ($talk) {
            return $talk;
        }

        return false;
    }
}
