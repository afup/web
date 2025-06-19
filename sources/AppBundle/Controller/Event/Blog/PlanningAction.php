<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Blog;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\JsonLd;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PlanningAction extends AbstractController
{
    public function __construct(
        private readonly JsonLd $jsonLd,
        private readonly EventActionHelper $eventActionHelper,
        private readonly TalkRepository $talkRepository,
    ) {}

    public function __invoke(Request $request, string $eventSlug): Response
    {
        $eventSlugs = explode(',', $eventSlug);
        $events = [];
        foreach ($eventSlugs as $eventSlug) {
            $event = $this->eventActionHelper->getEvent($eventSlug);
            $events[$event->getId()] = $event;
        }

        $applyPublicationDateFilters = $request->query->getBoolean('apply-publication-date-filters', true);

        $talkAggregates = $this->talkRepository->getByEventsWithSpeakers($events, $applyPublicationDateFilters);

        $jsonld = [];
        foreach ($events as $event) {
            $jsonld[] = $this->jsonLd->getDataForEvent($event);
        }

        $eventPlanning = [];
        $rooms = [];

        $hourMin = null;
        $hourMax = null;

        foreach ($talkAggregates as $talkAggregate) {
            $talk = $talkAggregate->talk;
            $planning = $talkAggregate->planning;
            $room = $talkAggregate->room;

            if ($planning === null) {
                continue;
            }

            $startDay = $planning->getStart()->format('d/m/Y');
            if (isset($eventPlanning[$startDay]) === false) {
                $eventPlanning[$startDay] = [];
            }
            $dateStart = $planning->getStart()->setTimezone(new \DateTimeZone('Europe/Paris'));
            $start = $dateStart->format('d/m/Y H:i');

            $dateEnd = $planning->getEnd()->setTimezone(new \DateTimeZone('Europe/Paris'));

            if ($dateStart->format('H') < $hourMin || $hourMin === null) {
                $hourMin = $dateStart->format('H');
            }
            if ($dateEnd->format('H') > $hourMax || $hourMax === null) {
                $hourMax = $dateEnd->format('H');
            }


            if (isset($eventPlanning[$startDay][$start]) === false) {
                $eventPlanning[$startDay][$start] = [];
            }

            $interval = $planning->getEnd()->diff($planning->getStart());

            $defaultProgramPagePrefix = '/';
            if (isset($events[$talk->getForumId()])) {
                $eventPath = $events[$talk->getForumId()]->getPath();
                $defaultProgramPagePrefix = $request->query->get('program-page-prefix-' . $eventPath, '/' . $eventPath . '/programme/');
            }

            $eventPlanning[$startDay][$start][$room->getId()][] = [
                'talk' => $talkAggregate->talk,
                'speakers' => $talkAggregate->speakers,
                'room' => $talkAggregate->room,
                'planning' => $talkAggregate->planning,
                'program_page_prefix' => $request->query->get('program-page-prefix', $defaultProgramPagePrefix),
                'length' => $interval->i + $interval->h * 60,
            ];

            if (isset($rooms[$room->getId()]) === false) {
                $rooms[$room->getId()] = $room;
            }
        }

        $hasAllEventsDisplayable = true;
        foreach ($events as $event) {
            if (false === $event->isPlanningDisplayable()) {
                $hasAllEventsDisplayable = false;
            }
        }

        return $this->render(
            'blog/planning.html.twig',
            [
                'planning' => $eventPlanning,
                'events' => $events,
                'planningDisplayable' => false === $applyPublicationDateFilters || $hasAllEventsDisplayable,
                'rooms' => $rooms,
                'hourMin' => $hourMin,
                'hourMax' => $hourMax,
                'precision' => 5,
                'jsonld' => $jsonld,
            ],
        );
    }
}
