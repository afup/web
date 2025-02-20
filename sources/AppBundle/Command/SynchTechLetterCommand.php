<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\TechLetter\MailchimpSynchronizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchTechLetterCommand extends Command
{
    private MailchimpSynchronizer $synchronizer;
    private LoggerInterface $logger;

    public function __construct(MailchimpSynchronizer $synchronizer, LoggerInterface $logger)
    {
        $this->synchronizer = $synchronizer;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('sync-techletter');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->synchronizer
           ->setLogger($this->logger)
           ->synchronize()
        ;

        return 0;
    }
}
