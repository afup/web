<?php


namespace AppBundle\Controller;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Talk;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends EventBaseController
{
    /**
     * @param Request $request
     * @param $eventSlug
     *
     * @return Response
     */
    public function programAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $jsonld = $this->get('app.event_json_ld')->getDataForEvent($event);
        $talks = $talkRepository->getByEventWithSpeakers($event, $request->query->getBoolean('apply-publication-date-filters', true));

        return $this->render(
            ':blog:program.html.twig',
            [
                'talks' => $talks,
                'event' => $event,
                'jsonld' => $jsonld,
                'speakersPagePrefix' => $request->query->get('speakers-page-prefix', '/' . $event->getPath() . '/speakers/'),
            ]
        );
    }

    /**
     * @param Request $request
     * @param $eventSlug
     *
     * @return Response
     */
    public function planningAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);
        $talks = $talkRepository->getByEventWithSpeakers($event, $request->query->getBoolean('apply-publication-date-filters', true));
        $jsonld = $this->get('app.event_json_ld')->getDataForEvent($event);

        $eventPlanning = [];
        $rooms = [];

        $hourMin = null;
        $hourMax = null;

        foreach ($talks as $talkWithData) {
            /**
             * @var $talk Talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var $planning Planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var $room Room
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

            $eventPlanning[$startDay][$start][$room->getId()] = $talkWithData;

            if (isset($rooms[$room->getId()]) === false) {
                $rooms[$room->getId()] = $room;
            }
        }

        return $this->render(
            ':blog:planning.html.twig',
                [
                    'planning' => $eventPlanning,
                    'event' => $event,
                    'rooms' => $rooms,
                    'hourMin' => $hourMin,
                    'hourMax' => $hourMax,
                    'precision' => 5,
                    'jsonld' => $jsonld
                ]
        );
    }

    public function talkWidgetAction(Request $request)
    {
        /**
         * @var $talkRepository TalkRepository
         */
        $talkRepository = $this->get('ting')->get(TalkRepository::class);

        $talks = $talkRepository->getBy(['id' => explode(',', $request->get('ids'))]);

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
            ':blog:talk.html.twig',
            [
                'talks_infos' => $talksInfos,
                'speakers' => $speakers,
                'widget_type' => $request->get('type', 'all')
            ]
        );
    }

    /**
     * @param Request $request
     * @param $eventSlug
     *
     * @return Response
     */
    public function speakersAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        /**
         * @var $speakerRepository SpeakerRepository
         */
        $speakerRepository = $this->get('ting')->get(SpeakerRepository::class);
        $speakers = $speakerRepository->getScheduledSpeakersByEvent($event, !$request->query->getBoolean('apply-publication-date-filters', true));
        $jsonld = $this->get('app.event_json_ld')->getDataForEvent($event);

        return $this->render(
            ':blog:speakers.html.twig',
            [
                'speakers' => $speakers,
                'event' => $event,
                'jsonld' => $jsonld,
                'programPagePrefix' => $request->query->get('program-page-prefix', '/' . $event->getPath() . '/programme/'),
            ]
        );
    }
}
