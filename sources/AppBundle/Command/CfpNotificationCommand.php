<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CfpNotificationCommand extends Command
{
    public function __construct(
        private readonly MessageFactory $messageFactory,
        private readonly SlackNotifier $slackNotifier,
        private readonly RepositoryFactory $ting,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('cfp-stats-notification')
            ->addOption('display-diff', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $eventRepository = $this->ting->get(EventRepository::class);
        $since = null;

        if ($input->getOption('display-diff')) {
            $since = new \DateTime();
            $since->modify('- 1 day');
        }

        $currentDate = new \DateTime();

        /** @var Event $event */
        foreach ($eventRepository->getNextEvents() as $event) {
            if ($currentDate > $event->getDateEndCallForPapers()) {
                continue;
            }

            $message = $this->messageFactory->createMessageForCfpStats(
                $event,
                $this->ting->get(TalkRepository::class),
                $this->ting->get(TalkToSpeakersRepository::class),
                $currentDate,
                $since
            );

            $this->slackNotifier->sendMessage($message);
        }

        return Command::SUCCESS;
    }
}
