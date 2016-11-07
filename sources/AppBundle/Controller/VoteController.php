<?php


namespace AppBundle\Controller;


use AppBundle\Event\Form\VoteType;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Event\Model\Vote;
use CCMBenchmark\Ting;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends EventBaseController
{
    private $formBuilder;

    /**
     * @param $eventSlug
     * @param bool $all if true => show all talks to rate even if already rated by the current user
     * @return Response
     */
    public function indexAction($eventSlug, $all = false)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event:cfp/closed.html.twig', ['event' => $event]);
        }

        /**
         * @var $talkRepository \AppBundle\Event\Model\Repository\TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);

        // Get a random list of unrated talks
        if ($all === false) {
            $talks = $talkRepository->getNewTalksToRate($event, $this->getUser());
        } else {
            $talks = $talkRepository->getTalksNotRatedByUser($event, $this->getUser());
        }

        $vote = new Vote();
        $forms = function () use ($talks, $vote, $eventSlug)
        {
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
     * @return Form
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
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
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


        /**
         * @var $talkRepository \AppBundle\Event\Model\Repository\TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        if ($talkRepository->getOneBy(['id' => $talkId]) === null) {
            return new JsonResponse(['errors' => ['Talk does not exists']], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $voteRepository VoteRepository
         */
        $voteRepository = $this->get('ting')->get(VoteRepository::class);
        /**
         * @var $vote Vote
         */
        $vote = $form->getData();
        $vote->setSubmittedOn(new \DateTime());

        try {
            $voteRepository->upsert($vote);
        } catch (Ting\Exception $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['errors' => []]);
    }

    public function adminAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($event === null) {
            throw $this->createNotFoundException(sprintf('Could not found event with slug %s', $eventSlug));
        }

        $votes = $this->get('ting')->get(VoteRepository::class)->getVotesByEvent($event->getId());

        return $this->render('admin/vote/liste.html.twig', ['votes' => $votes]);
    }

}
