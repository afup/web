<?php

declare(strict_types=1);

namespace AppBundle\Openfeedback;

use AppBundle\CFP\PhotoStorage;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;

class OpenfeedbackJsonGenerator
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PhotoStorage $photoStorage,
    ) {}

    public function generate(Event $event): array
    {
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);
        $data = [];
        foreach ($talkAggregates as $talkAggregate) {
            $speakersFormatted = [];
            $speakersId = [];
            foreach ($talkAggregate->speakers as $speaker) {
                $speakersId[] = "{$speaker->getId()}";
                $speakersFormatted[] = [
                    'name' => $speaker->getLabel(),
                    'photoUrl' => 'https://afup.org' . $this->photoStorage->getUrl($speaker),
                    'social' => [
                        [
                            'Présentation',
                            'link' => sprintf('https://event.afup.org/%s/speakers/#%d', $event->getPath(), $speaker->getId()),
                        ],
                    ],
                    'id' => "{$speaker->getId()}",
                ];
            }

            $talkFormatted = [
                'speakers' => $speakersId,
                'tags' => [$talkAggregate->talk->getTypeLabel()],
                'title' => $talkAggregate->talk->getTitle(),
                'id' => "{$talkAggregate->talk->getId()}",
                'trackTitle' => $talkAggregate->room?->getName() ?? '',
            ];

            if (null !== $talkAggregate->planning) {
                $talkFormatted['startTime'] = $this->getOpenfeedbackFormat($talkAggregate->planning->getStart());
                $talkFormatted['endTime'] = $this->getOpenfeedbackFormat($talkAggregate->planning->getEnd());
            }

            $data['sessions'][$talkAggregate->talk->getId()] = $talkFormatted;

            foreach ($speakersFormatted as $person) {
                $data['speakers'][$person["id"]] = $person;
            }
        }
        return $data;
    }

    private function getOpenfeedbackFormat(\DateTime $date): string
    {
        return $date->format('Y-m-d\TH:i:sP');
    }
}
