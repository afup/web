<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'speaker:expenses-files-clean')]
readonly class SpeakersExpensesFilesCleanCommand
{
    public function __construct(private SpeakersExpensesStorage $speakersExpensesStorage) {}

    public function __invoke(OutputInterface $output): int
    {
        $this->speakersExpensesStorage->cleanFiles();

        return Command::SUCCESS;
    }
}
