<?php

declare(strict_types=1);

namespace AppBundle\Command;

use PlanetePHP\FeedCrawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PlaneteCrawlFeeds extends Command
{
    public function __construct(private readonly FeedCrawler $feedReader)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('indexing:planete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->feedReader->crawl();

        return Command::SUCCESS;
    }
}
