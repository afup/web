<?php

namespace AppBundle\Command;

use AppBundle\Slack\UsersChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlackMemberNotificationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('slack-member-notification')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $results = $this->getContainer()->get(UsersChecker::class)->checkUsersValidity();

        if (($nbResults = count($results)) > 0) {
            $message = $this->getContainer()->get(\AppBundle\Slack\MessageFactory::class)->createMessageForMemberNotification($nbResults);
            $this->getContainer()->get(\AppBundle\Notifier\SlackNotifier::class)->sendMessage($message);
        }

        return 0;
    }
}
