<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\VideoNotifier\Engine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class VideoNotifierCommand extends Command
{
    private Engine $engine;

    public function __construct(Engine $engine)
    {
        parent::__construct();

        $this->engine = $engine;
    }

    protected function configure(): void
    {
        $this
            ->setName('videos:run-notifier')
            ->addOption('event-path', null, InputOption::VALUE_REQUIRED, 'Limiter les talks a un event particulier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entry = $this->engine->run($input->getOption('event-path'));

        if (null === $entry) {
            $io->warning('Aucun talk posté');
            return 0;
        }

        $io->success('Talk posté');
        $io->table(
            ['Talk ID', 'Bluesky ID', 'Mastodon ID'],
            [
                [$entry->getTalkId(), $entry->getStatusIdBluesky(), $entry->getStatusIdMastodon()],
            ],
        );

        return 0;
    }
}
