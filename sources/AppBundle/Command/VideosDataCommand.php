<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideosDataCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('videos:extract-data-for-youtube-import')
            ->addArgument('path', InputArgument::REQUIRED)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ting = $this->getContainer()->get('ting');

        /**
         * @var TalkRepository
         */
        $talkRepository = $ting->get(TalkRepository::class);

        /**
         * @var EventRepository
         */
        $eventRepository = $ting->get(EventRepository::class);

        $event = $eventRepository->getByPath($input->getArgument('path'));

        if (null === $event) {
            throw new \InvalidArgumentException("Event not found");
        }

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
             * @var $speakers Speaker[]
             */
            $speakers = $talkWithData['.aggregation']['speaker'];

            $speakersNames = [];
            foreach ($speakers as $speaker) {
                $speakersNames[] = $speaker->getLabel();
            }

            $data[] = [
                'filepath' => "",
                'title' => sprintf("%s - %s - %s", $talk->getTitle(), implode(',', $speakersNames), $event->getTitle()),
                "language" => $talk->getLanguageCode(),
                'url' => "https://afup.org/talks/" . $talk->getUrlKey(),
                'recording_date' => $planning->getStart()->format(\Datetime::ISO8601),
            ];
        }

        $output->writeln(json_encode($data, JSON_PRETTY_PRINT));

        return 0;
    }
}
