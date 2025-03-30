<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Ticket\RegistrationsExportGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegistrationsExporterCommand extends Command
{
    private EventRepository $eventRepository;
    private RegistrationsExportGenerator $exportGenerator;

    public function __construct(EventRepository $eventRepository,
                                RegistrationsExportGenerator $exportGenerator)
    {
        parent::__construct();
        $this->eventRepository = $eventRepository;
        $this->exportGenerator = $exportGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setName('export-registrations')
            ->addArgument('file', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === ($event = $this->eventRepository->getNextEvent())) {
            $output->writeln('No event found');
            return Command::SUCCESS;
        }

        $file = new \SplFileObject($input->getArgument('file'), 'w+');

        $this->exportGenerator->export($event, $file);

        return Command::SUCCESS;
    }
}
