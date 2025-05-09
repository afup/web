<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Planning;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideosDataCommand extends Command
{
    public function __construct(private readonly RepositoryFactory $ting)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('videos:extract-data-for-youtube-import')
            ->addArgument('path', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var TalkRepository $talkRepository */
        $talkRepository = $this->ting->get(TalkRepository::class);

        /** @var EventRepository $eventRepository */
        $eventRepository = $this->ting->get(EventRepository::class);

        $event = $eventRepository->getByPath($input->getArgument('path'));

        if (null === $event) {
            throw new \InvalidArgumentException("Event not found");
        }

        $talks = $talkRepository->getByEventWithSpeakers($event);

        $data = [];

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
             * @var Speaker[] $speakers
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

        return Command::SUCCESS;
    }
}
