<?php

namespace AppBundle\Command;

use Afup\Site\Forum\Inscriptions;
use AppBundle\Event\Model\Repository\EventRepository;
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
            ->addOption('event-path', null, InputOption::VALUE_REQUIRED)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $forum_inscriptions = new Inscriptions($GLOBALS['AFUP_DB']);
        $eventReposotory = $this->getContainer()->get('ting')->get(EventRepository::class);
        $ticketRepository = $this->getContainer()->get('ting')->get(TicketTypeRepository::class);

        $event = $this->getEventFilter($input);

        if (null === $event) {
            $event = $eventReposotory->getNextEvent();
        }

        if (null === $event) {
            return;
        }

        $date = null;

        if ($input->getOption('display-diff')) {
            $date = new \DateTime();
            $date->modify('- 1 day');
        }

        $message = $this->getContainer()->get('app.slack_message_factory')->createMessageForTicketStats(
            $event,
            $forum_inscriptions,
            $ticketRepository,
            $date
        );

        $this->getContainer()->get('app.slack_notifier')->sendMessage($message);
    }

    /**
     * @param InputInterface $input
     *
     * @return null
     */
    protected function getEventFilter(InputInterface $input)
    {
        if (null === ($eventPath = $input->getOption('event-path'))) {
            return null;
        }

        $event = $this
            ->getContainer()
            ->get('ting')
            ->get(EventRepository::class)
            ->getByPath($eventPath)
        ;

        if (null === $event) {
            throw new \InvalidArgumentException("L'événement sur lequel filter n'a pas été trouvé");
        }

        return $event;
    }
}
