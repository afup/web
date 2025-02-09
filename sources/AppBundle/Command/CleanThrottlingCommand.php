<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Security\ActionThrottling\ActionThrottling;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanThrottlingCommand extends ContainerAwareCommand
{
    private ActionThrottling $actionThrottling;
    public function __construct(ActionThrottling $actionThrottling)
    {
        $this->actionThrottling = $actionThrottling;
    }
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('throttling:clean')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->actionThrottling->clearOldLogs();

        return 0;
    }
}
