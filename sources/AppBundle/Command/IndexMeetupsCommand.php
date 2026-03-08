<?php

declare(strict_types=1);

namespace AppBundle\Command;

use Algolia\AlgoliaSearch\Exceptions\AlgoliaException;
use Algolia\AlgoliaSearch\SearchClient;
use AppBundle\Antennes\AntenneRepository;
use AppBundle\Event\Model\Repository\MeetupRepository;
use AppBundle\Indexation\Meetups\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexMeetupsCommand extends Command
{
    public function __construct(
        private readonly SearchClient $searchClient,
        private readonly MeetupRepository $meetupRepository,
        private readonly AntenneRepository $antenneRepository,
    ) {
        parent::__construct();
    }

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
        if ($input->getOption('run-scraping')) {
            $this->runScraping($output);
        }

        $runner = new Runner($this->searchClient, $this->meetupRepository, $this->antenneRepository);
        $runner->run();

        return Command::SUCCESS;
    }

    private function runScraping(OutputInterface $output): void
    {
        $greetInput = new ArrayInput([
            'command' => 'scrapping-meetup-event',
        ]);

        $this->getApplication()->doRun($greetInput, $output);
    }
}
