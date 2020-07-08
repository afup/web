<?php

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SpeakerListAction
{
    const VALID_SORTS = ['name', 'company'];
    const VALID_DIRECTIONS = ['asc', 'desc'];
    /** @var EventActionHelper */
    private $eventActionHelper;
    /** @var EventRepository */
    private $eventRepository;
    /** @var SpeakerRepository */
    private $speakerRepository;
    /** @var TalkRepository */
    private $talkRepository;
    /** @var Environment */
    private $twig;

    public function __construct(
        EventActionHelper $eventActionHelper,
        EventRepository $eventRepository,
        SpeakerRepository $speakerRepository,
        TalkRepository $talkRepository,
        Environment $twig
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->eventRepository = $eventRepository;
        $this->speakerRepository = $speakerRepository;
        $this->talkRepository = $talkRepository;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        Assertion::inArray($sort, self::VALID_SORTS);
        Assertion::inArray($direction, self::VALID_DIRECTIONS);
        $filter = $request->query->get('filter');
        $eventId = $request->query->get('eventId');
        $event = null;
        $speakers=[];
        $talks=[];
        if ($eventId !== null) {
            $event = $this->eventActionHelper->getEventById($eventId);
            $speakers = $this->speakerRepository->searchSpeakers($event, $sort, $direction, $filter);
            $talks = [];
            foreach ($speakers as $speaker) {
                $speakerTalks = [];
                foreach ($this->talkRepository->getTalksBySpeaker($event, $speaker) as $talk) {
                    if ($talk->getType() !== Talk::TYPE_PHP_PROJECT) {
                        $speakerTalks[$talk->getTitle()] = $talk;
                    }
                }
                ksort($speakerTalks);
                $talks[$speaker->getId()] = array_values($speakerTalks);
            }
        }
        /** @var Event[] $events */
        $events = $this->eventRepository->getAll();

        return new Response($this->twig->render('admin/speaker/list.html.twig', [
            'eventId' => $event === null ? null:$event->getId(),
            'events' => $events,
            'speakers' => $speakers,
            'talks' => $talks,
            'nbSpeakers' => $event === null ? 0:$this->speakerRepository->countByEvent($event),
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }
}
