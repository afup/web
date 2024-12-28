<?php

namespace AppBundle\Command;

use AppBundle\SpeakerInfos\SpeakersExpensesStorage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpeakersExpensesFilesCleanCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('speaker:expenses-files-clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $container->get(SpeakersExpensesStorage::class)
            ->setLogger($container->get('logger'))
            ->cleanFiles();
    }
}
