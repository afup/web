<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use AppBundle\Slack\UsersChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SlackMemberNotificationCommand extends Command
{
    private UsersChecker $usersChecker;
    private MessageFactory $messageFactory;
    private SlackNotifier $slackNotifier;
    public function __construct(UsersChecker $usersChecker, MessageFactory $messageFactory, SlackNotifier $slackNotifier)
    {
        $this->usersChecker = $usersChecker;
        $this->messageFactory = $messageFactory;
        $this->slackNotifier = $slackNotifier;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('slack-member-notification')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->usersChecker->checkUsersValidity();

        if (($nbResults = count($results)) > 0) {
            $message = $this->messageFactory->createMessageForMemberNotification((string) $nbResults);
            $this->slackNotifier->sendMessage($message);
        }

        return 0;
    }
}
