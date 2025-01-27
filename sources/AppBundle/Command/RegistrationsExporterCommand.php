<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Ticket\RegistrationsExportGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegistrationsExporterCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('export-registrations')
            ->addArgument('file', InputArgument::REQUIRED)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->getContainer();

        if (null === ($event = $container->get(EventRepository::class)->getNextEvent())) {
            $output->writeln('No event found');
            return 0;
        }

        $file = new \SplFileObject($input->getArgument('file'), 'w+');

        $container->get(RegistrationsExportGenerator::class)->export($event, $file);

        return 0;
    }
}
