<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Calendar\IcsPlanningGenerator;
use AppBundle\Calendar\JsonPlanningGenerator;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Openfeedback\OpenfeedbackJsonGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EventController extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
        private readonly EventRepository $eventRepository,
        private readonly VoteRepository $voteRepository,
        private readonly IcsPlanningGenerator $icsPlanningGenerator,
        private readonly JsonPlanningGenerator $jsonPlanningGenerator,
        private readonly OpenfeedbackJsonGenerator $openfeedbackJsonGenerator,
    ) {}

    public function index()
    {
        $events = $this->eventRepository->getNextEvents();

        if ($events === null) {
            return $this->render('event/none.html.twig');
        } elseif ($events->count() === 1) {
            $event = $events->first();
            return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->render('event/switch.html.twig', ['events' => $events]);
    }

    public function speakerInfosIndex()
    {
        $event = $this->eventRepository->getNextEventForGithubUser($this->getUser());

        if ($event === null) {
            return $this->render('event/none.html.twig');
        }

        return new RedirectResponse($this->generateUrl('speaker-infos', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function event($eventSlug): Response
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

    /**
     * @param $eventSlug
     */
    public function planningIcs($eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $response = new Response($this->icsPlanningGenerator->generateForEvent($event));

        $response->headers->add([
            'Content-Type' => 'text/Calendar; charset=UTF-8',
            'Content-Disposition' => sprintf('inline; filename=planning_%s.vcs', $event->getPath()),
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
        ]);

        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function planningJson($eventSlug)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        return new JsonResponse($this->jsonPlanningGenerator->generate($event));
    }

    /**
     * @param $eventSlug
     */
    public function calendar($eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }

        return $this->render('event/calendar.html.twig', ['event' => $event]);
    }

    /**
     * @return Response
     */
    public function calendarLatest()
    {
        $event = $this->eventRepository->getCurrentEvent();

        return new RedirectResponse($this->generateUrl('event_calendar', ['eventSlug' => $event->getPath()]));
    }

    public function openfeedbackJson($eventSlug): JsonResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        $response =  new JsonResponse($this->openfeedbackJsonGenerator->generate($event));

        $response->headers->set('Access-Control-Allow-Origin', 'https://openfeedback.io');

        return $response;
    }
}
