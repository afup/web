<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use AppBundle\Slack\UsersChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlackMemberNotificationCommand extends ContainerAwareCommand
{
    private UsersChecker $usersChecker;
    private MessageFactory $messageFactory;
    private SlackNotifier $slackNotifier;
    public function __construct(UsersChecker $usersChecker, MessageFactory $messageFactory, SlackNotifier $slackNotifier)
    {
        $this->usersChecker = $usersChecker;
        $this->messageFactory = $messageFactory;
        $this->slackNotifier = $slackNotifier;
    }
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('slack-member-notification')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->usersChecker->checkUsersValidity();

        if (($nbResults = count($results)) > 0) {
            $message = $this->messageFactory->createMessageForMemberNotification($nbResults);
            $this->slackNotifier->sendMessage($message);
        }

        return 0;
    }
}
