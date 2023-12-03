<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TicketStatsNotificationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('ticket-stats-notification')
            ->addOption('display-diff', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventReposotory = $this->getContainer()->get('ting')->get(EventRepository::class);
        $ticketRepository = $this->getContainer()->get('ting')->get(TicketTypeRepository::class);

        $date = null;

        if ($input->getOption('display-diff')) {
            $date = new \DateTime();
            $date->modify('- 1 day');
        }

        /** @var Event $event */
        foreach ($eventReposotory->getNextEvents() as $event) {
            $message = $this->getContainer()->get(\AppBundle\Slack\MessageFactory::class)->createMessageForTicketStats(
                $event,
                $this->getContainer()->get(EventStatsRepository::class),
                $ticketRepository,
                $date
            );

            $this->getContainer()->get(\AppBundle\Notifier\SlackNotifier::class)->sendMessage($message);
        }

        return 0;
    }
}
