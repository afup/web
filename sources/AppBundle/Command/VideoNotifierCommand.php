<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\VideoNotifier\Engine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class VideoNotifierCommand extends Command
{
    public function __construct(private readonly Engine $engine)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('videos:run-notifier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entry = $this->engine->run();

        if (null === $entry) {
            $io->warning('Aucun talk posté');
            return Command::SUCCESS;
        }

        $io->success('Talk posté');

        if ($output->isVerbose()) {
            $io->table(
                ['Talk ID', 'Bluesky ID', 'Mastodon ID'],
                [
                    [$entry->getTalkId(), $entry->getStatusIdBluesky(), $entry->getStatusIdMastodon()],
                ],
            );
        }

        return Command::SUCCESS;
    }
}
