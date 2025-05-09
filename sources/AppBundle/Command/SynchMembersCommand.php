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
    public function __construct(
        private readonly MailchimpMembersAutoListSynchronizer $synchronizer,
        private readonly LoggerInterface $logger,
    ) {
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

        return Command::SUCCESS;
    }
}
