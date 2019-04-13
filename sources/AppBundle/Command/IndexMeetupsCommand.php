<?php

namespace AppBundle\Command;

use AppBundle\Indexation\Meetups\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexMeetupsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('indexing:meetups')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $meetupClient = \DMS\Service\Meetup\MeetupKeyAuthClient::factory(['key' => $this->getContainer()->getParameter('meetup_api_key')]);

        $runner = new Runner($this->getContainer()->get(\AlgoliaSearch\Client::class), $meetupClient);
        $runner->run();
    }
}
