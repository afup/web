<?php

namespace AppBundle\Openfeedback;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;

class OpenfeedbackJsonGenerator
{
    /**
     * @var TalkRepository
     */
    private $talkRepository;

    /**
     * @var PhotoStorage
     */
    private $photoStorage;

    /**
     * @param TalkRepository $talkRepository
     * @param PhotoStorage $photoStorage
     */
    public function __construct(TalkRepository $talkRepository, PhotoStorage $photoStorage)
    {
        $this->talkRepository = $talkRepository;
        $this->photoStorage = $photoStorage;
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    public function generate(Event $event)
    {
        $talks = $this->talkRepository->getByEventWithSpeakers($event);
        $data = [];
        foreach ($talks as $talkWithData) {
            /**
             * @var Talk
             */
            $talk = $talkWithData['talk'];

            /**
             * @var Speaker[]
             */
            $speakers = $talkWithData['.aggregation']['speaker'];

            /**
             * @var Planning
             */
            $planning = $talkWithData['planning'];

            /**
             * @var Room
             */
            $room = $talkWithData['room'];

            $speakersFormatted = [];
            $speakersId = [];
            foreach ($speakers as $speaker) {
                $speakersId[] = "{$speaker->getId()}";
                $speakersFormatted[] = [
                    'name' => $speaker->getLabel(),
                    'photoUrl' => 'https://afup.org' . $this->photoStorage->getUrl($speaker),
                    'social' => [
                        [
                            'Présentation',
                            'link' => sprintf('https://event.afup.org/%s/speakers/#%d', $event->getPath(), $speaker->getId()),
                        ]
                    ],
                    'id' => "{$speaker->getId()}",
                ];
            }

            $talkFormatted = [
                'speakers' => $speakersId,
                'tags' => [$talk->getTypeLabel()],
                'title' => $talk->getTitle(),
                'id' => "{$talk->getId()}",
                'trackTitle' => ($room ? $room->getName() : ''),
            ];

            if (null !== $planning) {
                $talkFormatted ['startTime'] = $this->getOpenfeedbackFormat($planning->getStart());
                $talkFormatted ['endTime'] = $this->getOpenfeedbackFormat($planning->getEnd());
            }

            $data['sessions'][$talk->getId()] = $talkFormatted;

            if ($this->isNotSpeakerDuplicatation($data, $speakersFormatted)) {
                foreach ($speakersFormatted as $person) {
                    $data['speakers'][$person["id"]] = $person;
                }
            }
        }
        return $data;
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function getOpenfeedbackFormat(\DateTime $date)
    {
        return $date->format('Y-m-d\TH:i:sP');
    }

    /**
     * @param array|string $data
     * @param array $speakers
     * @return bool
     */
    public function isNotSpeakerDuplicatation($data, array $speakers)
    {
        return
            !is_array($data)
            || !array_key_exists('speakers', $data)
            || $speakers !== end($data['speakers']);
    }
}
