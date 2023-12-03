<?php

namespace AppBundle\Command;

use AppBundle\Indexation\Talks\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexTalksCommand extends Command
{
    /** @var Runner */
    private $runner;

    public function __construct(Runner $runner)
    {
        parent::__construct(null);
        $this->runner = $runner;
    }

    protected function configure()
    {
        $this
            ->setName('indexing:talks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runner->run();

        return 0;
    }
}
