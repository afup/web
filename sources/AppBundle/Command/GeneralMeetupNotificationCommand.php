<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use AppBundle\Notifier\SlackNotifier;
use AppBundle\Slack\MessageFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GeneralMeetupNotificationCommand extends Command
{
    /** @var UserRepository */
    private $userRepository;
    /** @var GeneralMeetingRepository */
    private $generalMeetingRepository;
    /** @var MessageFactory */
    private $messageFactory;
    /** @var SlackNotifier */
    private $slackNotifier;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        GeneralMeetingRepository $generalMeetingRepository,
        MessageFactory $messageFactory,
        SlackNotifier $slackNotifier,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->generalMeetingRepository = $generalMeetingRepository;
        $this->messageFactory = $messageFactory;
        $this->slackNotifier = $slackNotifier;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('general-meeting-notification');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->generalMeetingRepository->hasGeneralMeetingPlanned()) {
            $this->slackNotifier->sendMessage($this->messageFactory->createMessageForGeneralMeeting(
                $this->generalMeetingRepository,
                $this->userRepository,
                $this->urlGenerator
            ));
        }
    }
}
