<?php

namespace AppBundle\Command;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AppBundle\Event\Model\Repository\MeetupRepository;
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
     * @throws AlgoliaException
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $ting = $this->getContainer()->get('ting');

        /** @var Client $algoliaClient */
        $algoliaClient = $container->get(Client::class);
        $meetupRepository = $ting->get(MeetupRepository::class);

        $runner = new Runner($algoliaClient, $meetupRepository);
        $runner->run();
    }
}
