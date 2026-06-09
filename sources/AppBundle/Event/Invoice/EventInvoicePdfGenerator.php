<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;

class EventInvoicePdfGenerator
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly Pays $pays,
        private readonly BankAccountFactory $bankAccountFactory,
    ) {}

    public function generateQuote(string $reference, ?string $path = null): string
    {
        $facture = $this->invoiceRepository->getWithEventDataByReference($reference);
        $inscriptions = $this->ticketRepository->getWithTicketTypeByReference($reference);

        $dateFacture = isset($facture['date_facture']) && !empty($facture['date_facture'])
            ? new \DateTimeImmutable('@' . $facture['date_facture'])
            : new \DateTimeImmutable();

        $pdf = new PDF_Facture($this->bankAccountFactory->createApplyableAt($dateFacture));
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . ' ' . $facture['prenom'];
        }

        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Devis n°' . $reference);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $facture['societe'] . "\n" . $facture['adresse'] . "\n" . $facture['code_postal'] . "\n" . $facture['ville'] . "\n" . $this->pays->obtenirNom($facture['id_pays']));

        $pdf->Ln(15);
        $pdf->MultiCell(180, 5, sprintf("Devis concernant votre participation au %s organisé par l'Association Française des Utilisateurs de PHP (AFUP).", $facture['event_name']));

        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100, 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

        $total = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(50, 5, $this->truncate($inscription['pretty_name'], 27), 1);
            $pdf->Cell(100, 5, $inscription['prenom'] . ' ' . $inscription['nom'], 1);
            $pdf->Cell(40, 5, $inscription['montant'] . ' €', 1);
            $total += $inscription['montant'];
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(150, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, $total . ' €', 1, 0, 'L', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');

        if ($path !== null) {
            $pdf->Output($path, 'F');
            return '';
        }

        return (string) $pdf->Output('', 'S');
    }

    public function generateInvoice(string $reference, ?string $path = null): string
    {
        $facture = $this->invoiceRepository->getWithEventDataByReference($reference);
        $inscriptions = $this->ticketRepository->getWithTicketTypeByReference($reference);

        $dateFacture = isset($facture['date_facture']) && !empty($facture['date_facture'])
            ? new \DateTimeImmutable('@' . $facture['date_facture'])
            : new \DateTimeImmutable();

        $isSubjectedToVat = Vat::isSubjectedToVat($dateFacture);

        $pdf = new PDF_Facture($this->bankAccountFactory->createApplyableAt($dateFacture), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . ' ' . $facture['prenom'];
        }

        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Facture n°' . $reference);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $facture['societe'] . "\n" . $facture['adresse'] . "\n" . $facture['code_postal'] . "\n" . $facture['ville'] . "\n" . $this->pays->obtenirNom($facture['id_pays']));

        $pdf->Ln(15);
        $pdf->MultiCell(180, 5, sprintf("Facture concernant votre participation au %s organisé par l'Association Française des Utilisateurs de PHP (AFUP).", $facture['event_name']));

        if ($facture['informations_reglement']) {
            $pdf->Ln(10);
            $pdf->Cell(32, 5, 'Référence client : ');
            $infos = explode("\n", (string) $facture['informations_reglement']);
            foreach ($infos as $info) {
                $pdf->Cell(100, 5, $info);
                $pdf->Ln();
                $pdf->Cell(32, 5);
            }
        }

        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100 - ($isSubjectedToVat ? 35 : 0), 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell($isSubjectedToVat ? 30 : 40, 5, 'Prix' . ($isSubjectedToVat ? ' HT' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        if ($isSubjectedToVat) {
            $pdf->Cell(15, 5, 'TVA', 1, 0, 'C', 1);
            $pdf->Cell(30, 5, 'Prix TTC', 1, 0, 'R', 1);
        }

        $total = 0;
        $totalHt = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            if ($facture['event_has_prices_defined_with_vat']) {
                $montantHt = Vat::getRoundedWithoutVatPriceFromPriceWithVat($inscription['montant'], Utils::TICKETING_VAT_RATE);
                $montant = $inscription['montant'];
            } else {
                $montantHt = $inscription['montant'];
                $montant = Vat::getRoundedWithVatPriceFromPriceWithoutVat($inscription['montant'], Utils::TICKETING_VAT_RATE);
            }

            $pdf->Cell(50, 5, $this->truncate($inscription['pretty_name'], 27), 1);
            $pdf->Cell(100 - ($isSubjectedToVat ? 35 : 0), 5, $inscription['prenom'] . ' ' . $inscription['nom'], 1);
            $pdf->Cell(
                $isSubjectedToVat ? 30 : 40, 5,
                $this->formatValue($isSubjectedToVat ? $montantHt : $montant, $isSubjectedToVat) . ' €',
                1, 0, $isSubjectedToVat ? 'R' : '',
            );

            if ($isSubjectedToVat) {
                $pdf->Cell(15, 5, '10%', 1, 0, 'C');
                $pdf->Cell(30, 5, $this->formatValue($montant, $isSubjectedToVat) . ' €', 1, 0, 'R');
            }

            $totalHt += $montantHt;
            $total += $montant;
        }

        if ($facture['type_reglement'] == 1) {
            $pdf->Ln();
            $pdf->Cell(50, 5, 'FRAIS', 1);
            $pdf->Cell(100, 5, 'Paiement par chèque', 1);
            $pdf->Cell(40, 5, '25 €', 1);
            $total += 25;
        }

        $totalLabel = 'TOTAL' . ($isSubjectedToVat ? ' TTC' : '');

        if ($isSubjectedToVat) {
            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(160, 5, 'Total HT', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatValue($totalHt, $isSubjectedToVat) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(160, 5, 'Total TVA 10%', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatValue($total - $totalHt, $isSubjectedToVat) . ' €', 1, 0, 'R', 1);
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(150 + ($isSubjectedToVat ? 10 : 0), 5, $totalLabel, 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        $pdf->Cell(40 - ($isSubjectedToVat ? 10 : 0), 5, $this->formatValue($total, $isSubjectedToVat) . ' €', 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);

        $pdf->Ln(15);
        if ($facture['etat'] == 4) {
            $type = match ($facture['type_reglement']) {
                Ticket::PAYMENT_CREDIT_CARD => 'par CB',
                Ticket::PAYMENT_CHEQUE => 'par chèque',
                Ticket::PAYMENT_BANKWIRE => 'par virement',
                default => '',
            };
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(130, 5);
            if ($facture['type_reglement'] != Ticket::PAYMENT_NONE) {
                $pdf->Cell(60, 5, 'Payé ' . $type . ' le ' . date('d/m/Y', (int) $facture['date_reglement']));
            }
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->Ln();
        if (false === $isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }

        if ($path !== null) {
            $pdf->Output($path, 'F');
            return '';
        }

        return (string) $pdf->Output('', 'S');
    }

    private function truncate(string $value, int $length): string
    {
        if (strlen($value) <= $length) {
            return $value;
        }

        return substr($value, 0, $length) . '...';
    }

    private function formatValue(mixed $value, bool $isSubjectedToVat): string
    {
        if (false === $isSubjectedToVat) {
            return (string) $value;
        }

        return number_format((float) $value, 2, ',', ' ');
    }
}
