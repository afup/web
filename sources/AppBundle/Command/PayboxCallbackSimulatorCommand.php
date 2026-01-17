<?php

declare(strict_types=1);

namespace AppBundle\Command;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\MemberType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Payment\PayboxResponse;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(name: 'dev:paybox-callback-simulator')]
#[When('dev')]
class PayboxCallbackSimulatorCommand extends Command
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly EventRepository $eventRepository,
        private readonly Cotisations $cotisations,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $helper = new QuestionHelper();

        $style->title("Commande pour la simulation d'appel de Paybox");
        $style->info("Cette commande permet de simuler des appels de callback depuis Paybox.\nLaissez-vous guider par les questions.\n");

        $question = new ChoiceQuestion('Quel type de paiement souhaitez-vous simuler ?', [
            'Cotisation',
            'Évènement',
        ], 'Cotisation');
        $payementType = $helper->ask($input, $output, $question);

        if ($payementType === 'Cotisation') {
            $example = 'C2026-170120261126-0-1-ADMIN-84B';
            $regexp = '"^C\d{4}"';
        } else {
            $example = 'F202601-1701-JDOE-11f8d';
            $regexp = '"^F\d{4}"';
        }
        $question = new Question(sprintf('Pour quel identifiant de paiement (cmd) ? (par exemple: %s)', $example));
        $cmd = $helper->ask($input, $output, $question);
        $question->setValidator(function ($cmd) use ($regexp): void {
            if (!preg_match($regexp, $cmd)) {
                throw new \RuntimeException(
                    sprintf('Le format du CMD n\'est pas valide. Il doit être de la forme : %s', $regexp),
                );
            }
        });

        $question = new ChoiceQuestion('Quel statut de paiement ?', [
            'Validé',
            'Déjà effectué',
            'Annulé',
            'Refusé',
        ], 'Validé');
        $status = $helper->ask($input, $output, $question);

        if ($payementType === 'Cotisation') {
            $url = $this->callCotisation($cmd, $status);
        } else {
            $url = $this->callInvoice($cmd, $status);
        }

        $style->title('Résumé');
        $style->text([
            "Vous êtes sur le point de simuler un appel Paybox :",
            "<info>Type de paiement:</info> <comment>$payementType</comment>",
            "<info>Statut du paiement:</info> <comment>$status</comment>",
            "<info>CMD:</info> <comment>$cmd</comment>",
            "<info>URL:</info> <comment>$url</comment>",
            "",
        ]);

        $question = new ConfirmationQuestion('Êtes-vous sûr de vouloir faire cet appel Paybox (oui/non)?',false, '/^(y|o)/i');
        if ($helper->ask($input, $output, $question)) {
            $this->callCallback($url);
            $style->success('Appel Paybox effectué');
            return Command::SUCCESS;
        }
        $style->warning('Appel Paybox annulé');

        return Command::SUCCESS;
    }

    private function callCotisation(string $cmd, string $status): string
    {
        $account = $this->cotisations->getAccountFromCmd($cmd);
        $cotisation = $this->cotisations->obtenirDerniere(MemberType::from($account['type']), $account['id']);
        if (!$cotisation) {
            throw new \RuntimeException(
                sprintf('Cotisation non trouvée avec ce CMD: %s', $cmd),
            );
        }
        $url = $this->urlGenerator->generate('membership_payment');

        return $this->buildUrl($url, (float) $cotisation['montant'], $cmd, $status);
    }

    private function callInvoice(string $cmd, string $status): string
    {
        $invoice = $this->invoiceRepository->getByReference($cmd);
        if (!$invoice instanceof Invoice) {
            throw new \RuntimeException(
                sprintf('Facture non trouvée avec ce CMD: "%s"', $cmd),
            );
        }
        $event = $this->eventRepository->get($invoice->getForumId());
        if (!$event instanceof Event) {
            throw new \RuntimeException(
                sprintf('Évènement non trouvé avec ce CMD: "%s"', $cmd),
            );
        }

        $url = $this->urlGenerator->generate('ticket_paybox_callback', ['eventSlug' => $event->getPath()]);

        return $this->buildUrl($url, $invoice->getAmount(), $cmd, $status);
    }

    private function buildUrl(string $baseUrl, float $amount, string $cmd, string $state): string
    {
        $status = match ($state) {
            'Déjà effectué' => PayboxResponse::STATUS_DUPLICATE,
            'Annulé' => '00117',
            'Refusé' => '001XX',
            default => PayboxResponse::STATUS_SUCCESS,
        };

        $callBackParameters = [
            'total' => $amount * 100,
            'cmd' => $cmd,
            'autorisation' => 'fake_' . bin2hex(random_bytes(5)),
            'transaction' => random_int(400000, 600000),
            'status' => $status,
        ];

        return 'https://apachephp:80' . $baseUrl . '?' . http_build_query($callBackParameters);
    }

    private function callCallback(string $url): string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_exec($curl);

        return $url;
    }
}
