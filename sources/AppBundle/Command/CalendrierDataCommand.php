<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Room;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalendrierDataCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('calendrier:extract-data')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ting = $this->getContainer()->get('ting');

        $photoStorage = $this->getContainer()->get('app.photo_storage');

        /**
         * @var TalkRepository
         */
        $talkRepository = $ting->get(TalkRepository::class);

        /**
         * @var EventRepository
         */
        $eventRepository = $ting->get(EventRepository::class);

        $event = $eventRepository->getNextEvent();

        $talks = $talkRepository->getByEventWithSpeakers($event);

        $data = [];

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

            /**
             * @var $speakers Speaker[]
             */
            $speakers = $talkWithData['.aggregation']['speaker'];

            $conferenciers = [];
            foreach ($speakers as $speaker) {
                $conferenciers[] = [
                    'img' => 'https://afup.org' . $photoStorage->getUrl($speaker),
                    'link' => sprintf('https://event.afup.org/%s/speakers/#%d', $event->getPath(), $speaker->getId()),
                    'name' => $speaker->getLabel(),
                ];
            }

            $data[] = [
                'conferenciers' => $conferenciers,
                'date' => $planning->getStart()->format('d/m/Y H:i') . '-' . $planning->getEnd()->format('H:i'),
                'date_start' => $planning->getStart()->format(\Datetime::ISO8601),
                'date_end' => $planning->getEnd()->format(\Datetime::ISO8601),
                'detail' => strip_tags(html_entity_decode($talk->getAbstract())),
                'horaire' => $planning->getStart()->format('H:i') . '-' . $planning->getEnd()->format('H:i'),
                'id' => $talk->getId(),
                'lang' => $talk->getLanguageLabel(),
                'name' => $talk->getTitle(),
                'salle' => $room->getName(),
            ];
        }

        $output->writeln(json_encode($data, JSON_PRETTY_PRINT));
    }
}
