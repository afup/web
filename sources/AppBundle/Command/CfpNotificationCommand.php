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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CfpNotificationCommand extends ContainerAwareCommand
{
    private MessageFactory $messageFactory;
    private SlackNotifier $slackNotifier;
    private RepositoryFactory $ting;

    public function __construct(MessageFactory $messageFactory,
                                SlackNotifier $slackNotifier,
                                RepositoryFactory $ting)
    {
        $this->messageFactory = $messageFactory;
        $this->slackNotifier = $slackNotifier;
        $this->ting = $ting;
    }
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('cfp-stats-notification')
            ->addOption('display-diff', null, InputOption::VALUE_NONE)

        ;
    }

    /**
     * @see Command
     */
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

        return 0;
    }
}
