<?php

namespace AppBundle\Command;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Offices\OfficeFinder;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegistrationsByOfficeExporterCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('export-registrations-by-office')
            ->addArgument('file', InputArgument::REQUIRED)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventRepository = $this->getContainer()->get('ting')->get(EventRepository::class);
        $userRepository = $this->getContainer()->get('ting')->get(UserRepository::class);
        $invoiceRepository = $this->getContainer()->get('ting')->get(InvoiceRepository::class);
        $inscriptions = $this->getContainer()->get('app.legacy_model_factory')->createObject('\Afup\Site\Forum\Inscriptions');

        if (null === ($event = $eventRepository->getNextEvent())) {
            $output->writeln('No event found');
            return;
        }

        $curl = new CurlHttpAdapter();
        $geocoder = new GoogleMaps($curl, null, null, true, $this->getContainer()->getParameter('google_maps_api_key'));

        $file = new \SplFileObject($input->getArgument('file'), 'w+');

        $ticketLocator = new OfficeFinder($geocoder, $userRepository, $invoiceRepository, $inscriptions);

        $columns = [
            'id',
            'reference',
            'prenom',
            'nom',
            'societe',
            'tags',
            'type_pass',
            'email',
            'member_since',
            'office'
        ];

        $file->fputcsv($columns);

        foreach ($ticketLocator->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $file->fputcsv($preparedRow);
        }
    }
}
