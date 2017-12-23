<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneralMeetupNotificationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('general-meeting-notification')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assembleeGenerale = new \Afup\Site\Association\Assemblee_Generale($GLOBALS['AFUP_DB']);

        $message = $this->getContainer()->get('app.slack_message_factory')->createMessageForGeneralMeeting($assembleeGenerale);

        $this->getContainer()->get('app.slack_notifier')->sendMessage($message);
    }
}
