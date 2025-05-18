<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Controller\Admin\Event\AdminActionWithEventSelector;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Form\Support\EventSelectFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class SpeakerListAction implements AdminActionWithEventSelector
{
    public const VALID_SORTS = ['name', 'company'];
    public const VALID_DIRECTIONS = ['asc', 'desc'];

    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventRepository $eventRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly TalkRepository $talkRepository,
        private readonly Environment $twig,
        private readonly EventSelectFactory $eventSelectFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        Assertion::inArray($sort, self::VALID_SORTS);
        Assertion::inArray($direction, self::VALID_DIRECTIONS);
        $filter = $request->query->get('filter');
        $eventId = $request->query->get('id');

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

        /** @var Event[] $events */
        $events = $this->eventRepository->getAll();

        return new Response($this->twig->render('admin/speaker/list.html.twig', [
            'eventId' => $event === null ? null : $event->getId(),
            'event_select_form' => $this->eventSelectFactory->create($event, $request)->createView(),
            'events' => $events,
            'speakers' => $speakers,
            'talks' => $talks,
            'nbSpeakers' => $event === null ? 0 : $this->speakerRepository->countByEvent($event),
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }
}
