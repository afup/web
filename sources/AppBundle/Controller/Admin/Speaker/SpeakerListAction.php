<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Webmozart\Assert\Assert;

class SpeakerListAction
{
    public const VALID_SORTS = ['name', 'company'];
    public const VALID_DIRECTIONS = ['asc', 'desc'];

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly SpeakerRepository $speakerRepository,
        private readonly TalkRepository $talkRepository,
        private readonly Environment $twig,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');
        Assert::inArray($sort, self::VALID_SORTS);
        Assert::inArray($direction, self::VALID_DIRECTIONS);
        $filter = $request->query->get('filter');

        $event = $eventSelection->event;
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
            'eventId' => $event->getId(),
            'event_select_form' => $eventSelection->selectForm(),
            'events' => $events,
            'speakers' => $speakers,
            'talks' => $talks,
            'nbSpeakers' => $this->speakerRepository->countByEvent($event),
            'sort' => $sort,
            'direction' => $direction,
            'filter' => $filter,
        ]));
    }
}
