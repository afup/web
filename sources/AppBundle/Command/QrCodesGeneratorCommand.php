<?php

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\QrCodeGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QrCodesGeneratorCommand extends ContainerAwareCommand
{
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
            ->addOption('inscription-id-min', null, InputOption::VALUE_REQUIRED, 'Inscription ID minimum.')
            ->addOption('inscription-id-max', null, InputOption::VALUE_REQUIRED, 'Inscription ID maximum.')
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

        try {
            /** @var TicketRepository $ticketRepository */
            $ticketRepository = $this->getContainer()->get('ting')->get(TicketRepository::class);
            $tickets = $ticketRepository->getByEmptyQrCodes();

            if (count($tickets) === 0) {
                $io->text('Aucun nouveau code à générer');
                return 0;
            }

            $io->progressStart(count($tickets));
            foreach ($tickets as $ticket) {
                if (null !== ($inscriptionIdMin = $input->getOption('inscription-id-min')) && $inscriptionIdMin > $ticket->getId()) {
                    continue;
                }

                if (null !== ($inscriptionIdMax = $input->getOption('inscription-id-max')) && $inscriptionIdMax < $ticket->getId()) {
                    continue;
                }

                $io->progressAdvance();
                $ticket->setQrCode($this->qrCodeGenerator->generate($ticket->getId()));
                $ticketRepository->save($ticket);
            }
            $io->progressFinish();
        } catch (\Exception $e) {
            throw new \Exception('Problème lors de la génération des QR Codes', $e->getCode(), $e);
        }

        $io->success('Terminé avec succès');
        return 0;
    }
}
