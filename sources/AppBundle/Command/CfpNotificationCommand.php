<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CfpNotificationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('cfp-stats-notification')
            ->addOption('display-diff', null, InputOption::VALUE_NONE)
            ->addOption('event-path', null, InputOption::VALUE_REQUIRED)

        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ting = $this->getContainer()->get('ting');
        $eventReposotory = $this->getContainer()->get('ting')->get(EventRepository::class);

        $event = $this->getEventFilter($input);

        if (null === $event) {
            $event = $eventReposotory->getNextEvent();
        }

        if (null === $event) {
            return;
        }

        $since = null;

        if ($input->getOption('display-diff')) {
            $since = new \DateTime();
            $since->modify('- 1 day');
        }

        $currentDate = new \DateTime();

        $message = $this->getContainer()->get('app.slack_message_factory')->createMessageForCfpStats(
            $event,
            $ting->get(TalkRepository::class),
            $ting->get(TalkToSpeakersRepository::class),
            $currentDate,
            $since
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
