<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketStatsNotificationCommand extends Command
{
    private MessageFactory $messageFactory;
    private EventStatsRepository $eventStatsRepository;
    private SlackNotifier $slackNotifier;
    private RepositoryFactory $ting;

    public function __construct(MessageFactory $messageFactory,
                                EventStatsRepository $eventStatsRepository,
                                SlackNotifier $slackNotifier,
                                RepositoryFactory $ting)
    {
        $this->messageFactory = $messageFactory;
        $this->eventStatsRepository = $eventStatsRepository;
        $this->slackNotifier = $slackNotifier;
        $this->ting = $ting;
        parent::__construct();
    }
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('ticket-stats-notification')
            ->addOption('display-diff', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $eventReposotory = $this->ting->get(EventRepository::class);
        $ticketRepository = $this->ting->get(TicketTypeRepository::class);

        $date = null;

        if ($input->getOption('display-diff')) {
            $date = new \DateTime();
            $date->modify('- 1 day');
        }

        /** @var Event $event */
        foreach ($eventReposotory->getNextEvents() as $event) {
            $message = $this->messageFactory->createMessageForTicketStats(
                $event,
                $this->eventStatsRepository,
                $ticketRepository,
                $date
            );

            $this->slackNotifier->sendMessage($message);
        }

        return 0;
    }
}
