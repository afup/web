<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Event;
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

        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ting = $this->getContainer()->get('ting');
        $eventRepository = $this->getContainer()->get('ting')->get(EventRepository::class);

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

            $message = $this->getContainer()->get(\AppBundle\Slack\MessageFactory::class)->createMessageForCfpStats(
                $event,
                $ting->get(TalkRepository::class),
                $ting->get(TalkToSpeakersRepository::class),
                $currentDate,
                $since
            );

            $this->getContainer()->get(\AppBundle\Notifier\SlackNotifier::class)->sendMessage($message);
        }

        return 0;
    }
}
