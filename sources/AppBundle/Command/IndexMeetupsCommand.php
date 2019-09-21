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
        $container = $this->getContainer();

        $meetupClient = \DMS\Service\Meetup\MeetupOAuthClient::factory([
            'consumer_key' => $container->getParameter('meetup_api_consumer_key'),
            'consumer_secret' => $container->getParameter('meetup_api_consumer_secret'),
        ]);

        $runner = new Runner($container->get(\AlgoliaSearch\Client::class), $meetupClient);
        $runner->run();
    }
}
