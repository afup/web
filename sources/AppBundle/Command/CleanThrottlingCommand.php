<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Security\ActionThrottling\ActionThrottling;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanThrottlingCommand extends Command
{
    public function __construct(private readonly ActionThrottling $actionThrottling)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('throttling:clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->actionThrottling->clearOldLogs();

        return Command::SUCCESS;
    }
}
