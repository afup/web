<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
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
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ting = $this->getContainer()->get('ting');

        $event = $ting->get(EventRepository::class)->getNextEvent();

        if (null === $event) {
            return;
        }

        $since = new \DateTime();
        $since->modify('-1 day');

        $currentDate = new \DateTime();

        $message = $this->getContainer()->get('app.slack_message_factory')->createMessageForCfpStats(
            $event,
            $ting->get(TalkRepository::class),
            $ting->get(TalkToSpeakersRepository::class),
            $since,
            $currentDate
        );

        $this->getContainer()->get('app.slack_notifier')->sendMessage($message);
    }
}
