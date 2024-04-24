<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\QrCodeGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QrCodesGeneratorCommand extends ContainerAwareCommand
{
    use LockableTrait;

    /** @var QrCodeGenerator */
    private $qrCodeGenerator;

    public function __construct(QrCodeGenerator $qrCodeGenerator, $name = null)
    {
        parent::__construct($name);
        $this->qrCodeGenerator = $qrCodeGenerator;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate-qr-codes')
            ->setAliases(['g-q-c'])
            ->setDescription('Génère les QR codes pour les badges des participant.e.s pour les évènements.')
        ;
    }

    /**
     *
     * @see Command
     *
     * @throws \Exception
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Génération des QR codes pour les badges des participant.e.s pour les évènements.');

        if (!$this->lock()) {
            $io->warning('La commande est déjà en cours d\'exécution dans un autre processus.');

            return 0;
        }

        try {
            /** @var TicketRepository $ticketRepository */
            $ticketRepository = $this->getContainer()->get('ting')->get(TicketRepository::class);
            $tickets = $ticketRepository->getByEmptyQrCodes();

            if (count($tickets) === 0) {
                $io->text('Aucun nouveau code à générer');
                return 1;
            }

            $io->progressStart(count($tickets));
            foreach ($tickets as $ticket) {
                $io->progressAdvance();
                $ticket->setQrCode($this->qrCodeGenerator->generate($ticket->getId()));
                $ticketRepository->save($ticket);
            }

            $io->progressFinish();
            $io->success('Terminé avec succès');
            return 1;
        } catch (\Exception $e) {
            throw new \Exception('Problème lors de la génération des QR Codes', $e->getCode(), $e);
        }
    }
}
