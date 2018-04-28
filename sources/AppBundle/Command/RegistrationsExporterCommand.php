<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Ticket\RegistrationsExportGenerator;
use AppBundle\Offices\OfficeFinder;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegistrationsExporterCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('export-registrations')
            ->addArgument('file', InputArgument::REQUIRED)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $officeFinder = $this->getContainer()->get('app.offices_finder');

        $userRepository = $this->getContainer()->get('ting')->get(UserRepository::class);
        $invoiceRepository = $this->getContainer()->get('ting')->get(InvoiceRepository::class);
        $inscriptions = $this->getContainer()->get('app.legacy_model_factory')->createObject('\Afup\Site\Forum\Inscriptions');
        $exportGenerator = new RegistrationsExportGenerator($officeFinder, $inscriptions, $invoiceRepository, $userRepository);

        $eventRepository = $this->getContainer()->get('ting')->get(EventRepository::class);

        if (null === ($event = $eventRepository->getNextEvent())) {
            $output->writeln('No event found');
            return;
        }

        $file = new \SplFileObject($input->getArgument('file'), 'w+');

        $exportGenerator->export($event, $file);
    }
}
