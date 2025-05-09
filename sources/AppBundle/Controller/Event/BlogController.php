<?php

declare(strict_types=1);


namespace AppBundle\Controller\Event;

use AppBundle\Event\JsonLd;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function __construct(
        private readonly RepositoryFactory $repositoryFactory,
        private readonly JsonLd $jsonLd,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }

    public function program(Request $request, $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        /**
         * @var TalkRepository $talkRepository
         */
        $talkRepository = $this->repositoryFactory->get(TalkRepository::class);
        $jsonld = $this->jsonLd->getDataForEvent($event);
        $talks = $talkRepository->getByEventWithSpeakers($event, $request->query->getBoolean('apply-publication-date-filters', true));
        $now = new \DateTime();

        return $this->render(
            'blog/program.html.twig',
            [
                'talks' => iterator_to_array($talks),
                'event' => $event,
                'jsonld' => $jsonld,
                'speakersPagePrefix' => $request->query->get('speakers-page-prefix', '/' . $event->getPath() . '/speakers/'),
                'display_joindin_links' => $now >= $event->getDateStart() && $now <= \DateTimeImmutable::createFromMutable($event->getDateEnd())->modify('+10 days'),
            ]
        );
    }

    public function planning(Request $request, string $eventSlug): Response
    {
        $eventSlugs = explode(',', $eventSlug);
        $events = [];
        foreach ($eventSlugs as $eventSlug) {
            $event = $this->eventActionHelper->getEvent($eventSlug);
            $events[$event->getId()] = $event;
        }

        /**
         * @var TalkRepository $talkRepository
         */
        $talkRepository = $this->repositoryFactory->get(TalkRepository::class);
        $applyPublicationDateFilters = $request->query->getBoolean('apply-publication-date-filters', true);


        $talks = $talkRepository->getByEventsWithSpeakers($events, $applyPublicationDateFilters);

        $jsonld = [];
        foreach ($events as $event) {
            $jsonld[] = $this->jsonLd->getDataForEvent($event);
        }

        $eventPlanning = [];
        $rooms = [];

        $hourMin = null;
        $hourMax = null;

        foreach ($talks as $talkWithData) {
            /**
             * @var Talk $talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var Planning $planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var Room $room
             */
            $room = $talkWithData['room'];

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


            if (isset($eventPlanning[$startDay][$start])=== false) {
                $eventPlanning[$startDay][$start] = [];
            }

            $interval = $planning->getEnd()->diff($planning->getStart());
            $talkWithData['length'] = $interval->i + $interval->h * 60;


            $defaultProgramPagePrefix = '/';
            if (isset($events[$talk->getForumId()])) {
                $eventPath = $events[$talk->getForumId()]->getPath();
                $defaultProgramPagePrefix = $request->query->get('program-page-prefix-' . $eventPath, '/' . $eventPath . '/programme/');
            }
            $talkWithData['program_page_prefix'] = $request->query->get('program-page-prefix', $defaultProgramPagePrefix);
            $eventPlanning[$startDay][$start][$room->getId()][] = $talkWithData;

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
                ]
        );
    }

    public function talkWidget(Request $request): Response
    {
        /**
         * @var TalkRepository $talkRepository
         */
        $talkRepository = $this->repositoryFactory->get(TalkRepository::class);

        $talks = $talkRepository->getBy(['id' => explode(',', (string) $request->get('ids'))]);

        $speakers = [];
        $talksInfos = [];
        foreach ($talks as $talk) {
            foreach ($talkRepository->getByTalkWithSpeakers($talk) as $row) {
                $talksInfos[] = $row;
                foreach ($row['.aggregation']['speaker'] as $speaker) {
                    $speakers[$speaker->getId()] = $speaker;
                }
            }
        }

        return $this->render(
            'blog/talk.html.twig',
            [
                'talks_infos' => $talksInfos,
                'speakers' => $speakers,
                'widget_type' => $request->get('type', 'all'),
            ]
        );
    }

    public function speakers(Request $request, string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        /**
         * @var SpeakerRepository $speakerRepository
         */
        $speakerRepository = $this->repositoryFactory->get(SpeakerRepository::class);
        $speakers = $speakerRepository->getScheduledSpeakersByEvent($event, !$request->query->getBoolean('apply-publication-date-filters', true));
        $jsonld = $this->jsonLd->getDataForEvent($event);

        return $this->render(
            'blog/speakers.html.twig',
            [
                'speakers' => iterator_to_array($speakers),
                'event' => $event,
                'jsonld' => $jsonld,
                'programPagePrefix' => $request->query->get('program-page-prefix', '/' . $event->getPath() . '/programme/'),
            ]
        );
    }
}
