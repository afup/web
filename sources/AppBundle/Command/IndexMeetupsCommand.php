<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexMeetupsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('indexing:meetups')
            ->addOption('run-scraping')
        ;
    }

    /**
     * @throws AlgoliaException
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->getContainer();
        $ting = $this->getContainer()->get('ting');

        /** @var Client $algoliaClient */
        $algoliaClient = $container->get(Client::class);
        $meetupRepository = $ting->get(MeetupRepository::class);

        if ($input->getOption('run-scraping')) {
            $this->runScraping($output);
        }

        $runner = new Runner($algoliaClient, $meetupRepository);
        $runner->run();

        return 0;
    }

    private function runScraping(OutputInterface $output): void
    {
        $greetInput = new ArrayInput([
            'command' => 'scrapping-meetup-event',
        ]);

        $this->getApplication()->doRun($greetInput, $output);
    }
}
