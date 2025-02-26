<?php

declare(strict_types=1);

namespace AppBundle\Command;

use PlanetePHP\FeedCrawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PlaneteCrawlFeeds extends Command
{
    private FeedCrawler $feedReader;

    public function __construct(FeedCrawler $feedReader)
    {
        parent::__construct();

        $this->feedReader = $feedReader;
    }

    protected function configure(): void
    {
        $this
            ->setName('indexing:planete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->feedReader->crawl();

        return 0;
    }
}
