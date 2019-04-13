<?php

namespace AppBundle\Command;

use AppBundle\Indexation\Talks\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IndexTalksCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('indexing:talks')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runner = new Runner($this->getContainer()->get(\AlgoliaSearch\Client::class), $this->getContainer()->get('ting'));
        $runner->run();
    }
}
