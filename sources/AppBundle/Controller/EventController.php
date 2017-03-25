<?php


namespace AppBundle\Controller;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\VoteRepository;
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

    public function eventAction($eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);
        if ($event->getDateEndCallForPapers() < new \DateTime()) {
            return $this->render(':event/cfp:closed.html.twig', ['event' => $event]);
        }

        $talks = $this->get('ting')->get(TalkRepository::class)->getNumberOfTalksByEvent($event);
        $votes = $this->get('ting')->get(VoteRepository::class)->getNumberOfVotesByEvent($event);

        return $this->render(':event:home.html.twig', ['event' => $event, 'talks' => $talks['talks'], 'votes' => $votes['votes']]);
    }
}
