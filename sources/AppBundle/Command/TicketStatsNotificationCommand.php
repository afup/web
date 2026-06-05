<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketStatsNotificationCommand extends Command
{
    public function __construct(
        private readonly MessageFactory $messageFactory,
        private readonly EventStatsRepository $eventStatsRepository,
        private readonly SlackNotifier $slackNotifier,
        private readonly EventRepository $eventRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {
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
            ->addOption('dry-run', null, InputOption::VALUE_NONE, "Affiche les messages sans les envoyer à Slack")
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = null;

        if ($input->getOption('display-diff')) {
            $date = new \DateTime();
            $date->modify('- 1 day');
        }

        $dryRun = (bool) $input->getOption('dry-run');

        /** @var Event $event */
        foreach ($this->eventRepository->getNextEvents() as $event) {
            $message = $this->messageFactory->createMessageForTicketStats(
                $event,
                $this->eventStatsRepository,
                $this->ticketTypeRepository,
                $date,
            );

            if ($dryRun) {
                $output->writeln(sprintf('<info>[%s] %s</info>', $message->getChannel(), $message->getUsername()));
                foreach ($message->getAttachments() as $attachment) {
                    $output->writeln(sprintf('  %s', $attachment->getTitle()));
                    foreach ($attachment->getFields() as $field) {
                        $output->writeln(sprintf('    - %s : %s', $field->getTitle(), $field->getValue()));
                    }
                }
                continue;
            }

            $this->slackNotifier->sendMessage($message);
        }

        return Command::SUCCESS;
    }
}
