<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideosDataCommand extends Command
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly EventRepository $eventRepository,
    ) {
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
        $event = $this->eventRepository->getByPath($input->getArgument('path'));

        if (null === $event) {
            throw new \InvalidArgumentException("Event not found");
        }

        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);

        $data = [];

        foreach ($talkAggregates as $talkAggregate) {
            $speakersNames = [];
            foreach ($talkAggregate->speakers as $speaker) {
                $speakersNames[] = $speaker->getLabel();
            }

            $data[] = [
                'filepath' => "",
                'title' => sprintf("%s - %s - %s", $talkAggregate->talk->getTitle(), implode(',', $speakersNames), $event->getTitle()),
                "language" => $talkAggregate->talk->getLanguageCode(),
                'url' => "https://afup.org/talks/" . $talkAggregate->talk->getUrlKey(),
                'recording_date' => $talkAggregate->planning->getStart()->format(\Datetime::ISO8601),
            ];
        }

        $output->writeln(json_encode($data, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
