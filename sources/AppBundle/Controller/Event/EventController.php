<?php

namespace AppBundle\Controller\Event;

use AppBundle\Calendar\IcsPLanningGenerator;
use AppBundle\Calendar\JsonPlanningGenerator;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use AppBundle\Openfeedback\OpenfeedbackJsonGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EventController extends EventBaseController
{
    public function indexAction()
    {
        /**
         * @var EventRepository $eventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $events = $eventRepository->getNextEvents();

        if ($events === null) {
            return $this->render(':event:none.html.twig');
        } elseif ($events->count() === 1) {
            $event = $events->first();
            return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
        }

        return $this->render(':event:switch.html.twig', ['events' => $events]);
    }

    public function speakerInfosIndexAction()
    {
        /**
         * @var EventRepository $eventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getNextEventForGithubUser($this->getUser());

        if ($event === null) {
            return $this->render(':event:none.html.twig');
        }

        return new RedirectResponse($this->generateUrl('speaker-infos', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function eventAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        $talks = $this->get('ting')->get(TalkRepository::class)->getNumberOfTalksByEvent($event);
        $votes = $this->get('ting')->get(VoteRepository::class)->getNumberOfVotesByEvent($event);

        $currentDate = new \DateTime();

        if ($event->getDateEndCallForPapers() < $currentDate) {
            if (!$event->isVoteAvailable()) {
                return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
            }

            return $this->render(':event/cfp:vote_only.html.twig', ['event' => $event, 'talks' => $talks['talks'], 'votes' => $votes['votes']]);
        }

        return $this->render(':event:home.html.twig', ['event' => $event, 'talks' => $talks['talks'], 'votes' => $votes['votes']]);
    }

    /**
     * @param $eventSlug
     *
     * @return Response
     */
    public function planningIcsAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        $icsPlanningGenerator = new IcsPLanningGenerator($this->get('ting')->get(TalkRepository::class));

        $response = new Response($icsPlanningGenerator->generateForEvent($event));

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
    public function planningJsonAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        $photoStorage = $this->get(\AppBundle\CFP\PhotoStorage::class);
        $ting = $this->get('ting');
        $talkRepository = $ting->get(TalkRepository::class);

        $jsonPlanningGenerator = new JsonPlanningGenerator($talkRepository, $photoStorage);

        return new JsonResponse($jsonPlanningGenerator->generate($event));
    }

    /**
     * @param $eventSlug
     *
     * @return Response
     */
    public function calendarAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($event === null) {
            throw $this->createNotFoundException('Event not found');
        }

        return $this->render(':event:calendar.html.twig', ['event' => $event]);
    }

    /**
     * @return Response
     */
    public function calendarLatestAction()
    {
        $event = $this->get('ting')->get(EventRepository::class)->getCurrentEvent();

        return new RedirectResponse($this->generateUrl('event_calendar', ['eventSlug' => $event->getPath()]));
    }

    /**
     * @return JsonResponse
     */
    public function openfeedbackJsonAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        $photoStorage = $this->get(\AppBundle\CFP\PhotoStorage::class);
        $ting = $this->get('ting');
        $talkRepository = $ting->get(TalkRepository::class);

        $generator = new OpenfeedbackJsonGenerator($talkRepository, $photoStorage);

        $response =  new JsonResponse($generator->generate($event));

        $response->headers->set('Access-Control-Allow-Origin', 'https://openfeedback.io');

        return $response;
    }
}
