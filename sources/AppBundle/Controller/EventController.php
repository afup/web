<?php


namespace AppBundle\Controller;

use AppBundle\Calendar\IcsPLanningGenerator;
use AppBundle\Calendar\JsonPlanningGenerator;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EventController extends EventBaseController
{
    public function indexAction()
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getNextEvent();

        if ($event === null) {
            return $this->render(':event:none.html.twig');
        }
        return new RedirectResponse($this->generateUrl('event', ['eventSlug' => $event->getPath()]), Response::HTTP_TEMPORARY_REDIRECT);
    }

    public function speakerInfosIndexAction()
    {
        /**
         * @var $eventRepository EventRepository
         */
        $eventRepository = $this->get('ting')->get(EventRepository::class);
        $event = $eventRepository->getNextEvent();

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

        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            if ($event->getDateEndVote() < new \DateTime()) {
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
    public function planningJsonAction()
    {
        $photoStorage = $this->get('app.photo_storage');
        $ting = $this->get('ting');
        $talkRepository = $ting->get(TalkRepository::class);
        $eventRepository = $ting->get(EventRepository::class);

        $event = $eventRepository->getCurrentEvent();

        $jsonPlanningGenerator = new JsonPlanningGenerator($talkRepository, $photoStorage);

        return new JsonResponse($jsonPlanningGenerator->generate($event));
    }
}
