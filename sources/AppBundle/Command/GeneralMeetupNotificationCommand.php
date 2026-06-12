<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\AssembleeGenerale\Entity\Repository\AssembleeGeneraleRepository;
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
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AssembleeGeneraleRepository $assembleGeneraleRepository,
        private readonly GeneralMeetingRepository $generalMeetingRepository,
        private readonly MessageFactory $messageFactory,
        private readonly SlackNotifier $slackNotifier,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('general-meeting-notification');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->assembleGeneraleRepository->hasPlanned()) {
            $this->slackNotifier->sendMessage($this->messageFactory->createMessageForGeneralMeeting(
                $this->generalMeetingRepository,
                $this->userRepository,
                $this->urlGenerator,
            ));
        }

        return Command::SUCCESS;
    }
}
