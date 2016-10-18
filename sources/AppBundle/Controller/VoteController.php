<?php


namespace AppBundle\Controller;


use AppBundle\Form\VoteType;
use AppBundle\Model\Event;
use AppBundle\Model\Repository\EventRepository;
use AppBundle\Model\Repository\TalkRepository;
use AppBundle\Model\Repository\VoteRepository;
use AppBundle\Model\Talk;
use AppBundle\Model\Vote;
use CCMBenchmark\Ting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoteController extends Controller
{
    private $formBuilder;

    public function indexAction($eventSlug)
    {
        /**
         * @var $eventRepository \AppBundle\Model\Repository\EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        /**
         * @var $event Event|null
         */
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException(sprintf('Could not found event with slug %s', $eventSlug));
        }
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event:cfp/closed.html.twig', ['event' => $event]);
        }

        /**
         * @var $talkRepository \AppBundle\Model\Repository\TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);

        // Get a random list of 10 talks
        $talks = $talkRepository->getTalksToRateByEvent($event, $this->getUser());

        $vote = new Vote();
        $forms = function () use ($talks, $vote, $eventSlug)
        {
            /**
             * @var $talk Talk
             */
            foreach ($talks as $talk) {
                $myVote = clone $vote;

                /*
                 * By using a yield here, there will be only one iteration over the talks for the entire page
                 */
                yield [
                    'form' => $this->createVoteForm($eventSlug, $talk->getId(), $myVote)->createView(),
                    'talk' => $talk
                ];
            }
        };

        return $this->render(
            'event/vote/liste.html.twig',
            [
                'numberOfTalks' => $talks->count(),
                'talks' => $forms()
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
        /**
         * @var $eventRepository \AppBundle\Model\Repository\EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        /**
         * @var $event Event|null
         */
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException(sprintf('Could not found event with slug %s', $eventSlug));
        }
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
         * @var $talkRepository \AppBundle\Model\Repository\TalkRepository
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

    public function adminAction(Request $request, $eventSlug)
    {
        /**
         * @var $eventRepository \AppBundle\Model\Repository\EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        /**
         * @var $event Event|null
         */
        $event = $eventRepository->getOneBy(['path' => $eventSlug]);

        if ($event === null) {
            throw $this->createNotFoundException(sprintf('Could not found event with slug %s', $eventSlug));
        }

        $votes = $this->get('ting')->get(VoteRepository::class)->getVotesByEvent($event->getId());

        return $this->render('admin/vote/liste.html.twig', ['votes' => $votes]);
    }

}
