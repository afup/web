<?php

declare(strict_types=1);

namespace AppBundle\Command;

use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Ticket\QrCodeGenerator;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class QrCodesGeneratorCommand extends Command
{
    public function __construct(
        private readonly QrCodeGenerator $qrCodeGenerator,
        private readonly RepositoryFactory $ting,
    ) {
        parent::__construct();
    }

    protected function configure(): void
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Génération des QR codes pour les badges des participant.e.s pour les évènements.');

        try {
            /** @var TicketRepository $ticketRepository */
            $ticketRepository = $this->ting->get(TicketRepository::class);
            $tickets = $ticketRepository->getByEmptyQrCodes();

            if (count($tickets) === 0) {
                $io->text('Aucun nouveau code à générer');
                return Command::SUCCESS;
            }

            $io->progressStart(count($tickets));
            foreach ($tickets as $ticket) {
                $inscriptionIdMin = (int) $input->getOption('inscription-id-min');
                $inscriptionIdMax = (int) $input->getOption('inscription-id-max');

                if ($inscriptionIdMin > $ticket->getId()) {
                    continue;
                }

                if ($inscriptionIdMax < $ticket->getId()) {
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
        return Command::SUCCESS;
    }
}
