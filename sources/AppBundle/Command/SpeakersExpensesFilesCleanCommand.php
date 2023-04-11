<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpeakersExpensesFilesCleanCommand extends Command
{
    private SpeakersExpensesStorage $speakersExpensesStorage;
    private LoggerInterface $logger;
    protected function configure(): void
    {
        $this->setName('speaker:expenses-files-clean');
    }
    public function __construct(SpeakersExpensesStorage $speakersExpensesStorage, LoggerInterface $logger)
    {
        $this->speakersExpensesStorage = $speakersExpensesStorage;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->speakersExpensesStorage->cleanFiles($this->logger);

        return self::SUCCESS;
    }
}
