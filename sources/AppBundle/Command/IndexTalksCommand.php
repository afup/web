<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Indexation\Talks\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexTalksCommand extends Command
{
    private Runner $runner;

    public function __construct(Runner $runner)
    {
        parent::__construct();
        $this->runner = $runner;
    }

    protected function configure(): void
    {
        $this
            ->setName('indexing:talks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->runner->run();

        return 0;
    }
}
