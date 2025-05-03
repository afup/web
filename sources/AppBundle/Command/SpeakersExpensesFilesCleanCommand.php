<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'speaker:expenses-files-clean')]
class SpeakersExpensesFilesCleanCommand extends Command
{
    public function __construct(private readonly SpeakersExpensesStorage $speakersExpensesStorage)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->speakersExpensesStorage->cleanFiles();

        return self::SUCCESS;
    }
}
