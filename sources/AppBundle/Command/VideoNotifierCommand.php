<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\VideoNotifier\Engine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->setName('plop') // todo
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->engine->run();

        return 0;
    }
}
