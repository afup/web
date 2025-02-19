<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Mailchimp\MailchimpMembersAutoListSynchronizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchMembersCommand extends Command
{
    private MailchimpMembersAutoListSynchronizer $synchronizer;
    private LoggerInterface $logger;

    public function __construct(MailchimpMembersAutoListSynchronizer $synchronizer, LoggerInterface $logger)
    {
        $this->synchronizer = $synchronizer;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('sync-members');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->synchronizer
            ->setLogger($this->logger)
            ->synchronize();

        return 0;
    }
}
