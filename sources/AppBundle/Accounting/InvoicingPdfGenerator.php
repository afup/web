<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Vat;
use AppBundle\Accounting\Model\Invoicing;
use AppBundle\Accounting\Model\InvoicingDetail;
use AppBundle\Compta\BankAccount\BankAccountFactory;

class InvoicingPdfGenerator
{
    public function __construct(private readonly Pays $pays) {}

    public function generateInvoice(Invoicing $invoicing, ?string $path = null): string
    {
        $date = $invoicing->getInvoiceDate() !== null
            ? \DateTimeImmutable::createFromMutable($invoicing->getInvoiceDate())
            : new \DateTimeImmutable();

        $isSubjectedToVat = Vat::isSubjectedToVat($date);
        $pdf = $this->buildPdf($date, $isSubjectedToVat);
        $pdf->AddPage();

        $this->renderHeader($pdf, $date->format('d/m/Y'));
        $this->renderRecipient($pdf, $invoicing);

        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, 'Facture n° ' . $invoicing->getInvoiceNumber(), 0, 0, 'C');
        $pdf->SetFont('Arial', '', 10);

        $this->renderClientReferences($pdf, $invoicing);

        $pdf->MultiCell(180, 5, 'Comme convenu, nous vous prions de trouver votre facture');

        $devise = $this->currencySymbol($invoicing);
        [$totalHt, $totalTtc, $vatAmounts] = $this->renderLineItems($pdf, $invoicing->getDetails(), $isSubjectedToVat, $devise, true);

        $this->renderTotals($pdf, $isSubjectedToVat, $totalHt, $totalTtc, $vatAmounts, $devise);

        $pdf->Ln(15);
        if (!$isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }

        $pdf->Ln();
        $pdf->Cell(10, 5, 'Payable à réception.');
        if ($date >= new \DateTimeImmutable('2025-01-01')) {
            $pdf->Ln();
            $pdf->MultiCell(190, 5, "Pénalités pour retard de paiement, 3 fois le taux d'intérêt légal sur les sommes dues.\nIndemnité forfaitaire pour frais de recouvrement de 40€.\nPas d'escompte en cas de paiement anticipé.\n");
        }

        $pdf->Ln(10);
        if ($invoicing->getObservation() !== '') {
            $pdf->Cell(10, 5, 'Observations : ');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 8);
            $pdf->MultiCell(130, 5, $invoicing->getObservation());
        }

        return $this->output($pdf, $path, $this->getInvoiceFilename($invoicing));
    }

    public function generateQuotation(Invoicing $invoicing, ?string $path = null): string
    {
        $date = $invoicing->getQuotationDate() !== null
            ? \DateTimeImmutable::createFromMutable($invoicing->getQuotationDate())
            : new \DateTimeImmutable();

        $isSubjectedToVat = Vat::isSubjectedToVat($date);
        $pdf = $this->buildPdf($date, $isSubjectedToVat);
        $pdf->AddPage();

        $this->renderHeader($pdf, $date->format('d/m/Y'));
        $this->renderRecipient($pdf, $invoicing);

        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, 'Devis n° ' . $invoicing->getQuotationNumber(), 0, 0, 'C');
        $pdf->SetFont('Arial', '', 10);

        $this->renderClientReferences($pdf, $invoicing);

        $pdf->MultiCell(180, 5, 'Comme convenu, nous vous prions de trouver votre devis');

        $devise = $this->currencySymbol($invoicing);
        [$totalHt, $totalTtc, $vatAmounts] = $this->renderLineItems($pdf, $invoicing->getDetails(), $isSubjectedToVat, $devise, false);

        $this->renderTotals($pdf, $isSubjectedToVat, $totalHt, $totalTtc, $vatAmounts, $devise);

        $pdf->Ln(15);
        if (!$isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }
        $pdf->Ln(10);
        $pdf->Cell(10, 5, 'Observations : ');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(130, 5, $invoicing->getObservation());

        return $this->output($pdf, $path, $this->getQuotationFilename($invoicing));
    }

    public function getInvoiceFilename(Invoicing $invoicing): string
    {
        return 'Facture - ' . $invoicing->getCompany() . ' - ' . ($invoicing->getInvoiceDate() ? $invoicing->getInvoiceDate()->format('Y-m-d') : '') . '.pdf';
    }

    public function getQuotationFilename(Invoicing $invoicing): string
    {
        return 'Devis - ' . $invoicing->getCompany() . ' - ' . ($invoicing->getQuotationDate() ? $invoicing->getQuotationDate()->format('Y-m-d') : '') . '.pdf';
    }

    private function buildPdf(\DateTimeImmutable $date, bool $isSubjectedToVat): PDF_Facture
    {
        $bankAccountFactory = new BankAccountFactory();
        return new PDF_Facture($bankAccountFactory->createApplyableAt($date), $isSubjectedToVat);
    }

    private function renderHeader(PDF_Facture $pdf, string $formattedDate): void
    {
        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $formattedDate);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
    }

    private function renderRecipient(PDF_Facture $pdf, Invoicing $invoicing): void
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5,
            $invoicing->getCompany() . "\n"
            . $invoicing->getService() . "\n"
            . $invoicing->getAddress() . "\n"
            . $invoicing->getZipcode() . ' '
            . $invoicing->getCity() . "\n"
            . $this->pays->obtenirNom($invoicing->getCountryId())
            . ($invoicing->getTvaIntra() ? ("\nN° TVA Intracommunautaire : " . $invoicing->getTvaIntra()) : ''),
        );
        $pdf->Ln(10);
    }

    private function renderClientReferences(PDF_Facture $pdf, Invoicing $invoicing): void
    {
        if ($invoicing->getRefClt1() !== '' || $invoicing->getRefClt2() !== '' || $invoicing->getRefClt3() !== '') {
            $pdf->Ln(15);
            $pdf->Cell(40, 5, 'Repère(s) : ');
        }
        foreach ([$invoicing->getRefClt1(), $invoicing->getRefClt2(), $invoicing->getRefClt3()] as $ref) {
            if ($ref !== '') {
                $pdf->setx(30);
                $pdf->Cell(100, 5, $ref);
                $pdf->Ln(5);
            }
        }
        $pdf->Ln(10);
    }

    /**
     * Renders the line-items table and returns [totalHt, totalTtc, vatAmounts].
     *
     * @param InvoicingDetail[] $details
     * @return array{float, float, array<string, float>}
     */
    private function renderLineItems(PDF_Facture $pdf, array $details, bool $isSubjectedToVat, string $devise, bool $drawColumnLines): array
    {
        $pdf->Ln(5);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(30, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell($isSubjectedToVat ? 60 : 80, 5, 'Description', 1, 0, 'L', 1);
        $pdf->Cell(20, 5, 'Quantite', 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        if ($isSubjectedToVat) {
            $pdf->Cell(20, 5, 'TVA', 1, 0, 'C', 1);
        }
        $pdf->Cell(30, 5, 'Prix' . ($isSubjectedToVat ? ' HT' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        $pdf->Cell(30, 5, 'Total' . ($isSubjectedToVat ? ' TTC' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);

        $totalHt = 0.0;
        $totalTtc = 0.0;
        $vatAmounts = [];
        $yInitial = $pdf->getY();
        $columns = $isSubjectedToVat ? [0, 30, 90, 110, 130, 160, 190] : [0, 30, 110, 130, 160, 190];

        foreach ($details as $detail) {
            if ((float) $detail->getQuantity() === 0.0) {
                continue;
            }

            $montantHt = $detail->getQuantity() * $detail->getUnitPrice();
            $montantTtc = $montantHt;

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            if (!$drawColumnLines && !$isSubjectedToVat) {
                $pdf->Cell(30, 5, $detail->getReference(), 1);
                $pdf->Cell(80, 5, $detail->getDesignation(), 1);
                $pdf->Cell(20, 5, number_format((float) $detail->getQuantity(), 2, '.', ''), 1, 0, 'C');
                $pdf->Cell(30, 5, number_format($detail->getUnitPrice(), 2, '.', '') . $devise, 1, 0, 'R');
                $pdf->Cell(30, 5, $this->formatValue($montantHt, false) . $devise, 1, 0, 'R');
            } else {
                $y = $pdf->GetY();
                $x = $pdf->GetX();

                $pdf->MultiCell(30, 5, $detail->getReference(), 'T');
                $x += 30;
                $pdf->SetXY($x, $y);

                $designationLength = $isSubjectedToVat ? 60 : 80;
                $pdf->MultiCell($designationLength, 5, $detail->getDesignation(), 'T');
                $x += $designationLength;
                $pdf->SetXY($x, $y);

                $pdf->MultiCell(20, 5, number_format((float) $detail->getQuantity(), 2, '.', ''), 'T', 0, 'C');
                $x += 20;

                if ($isSubjectedToVat) {
                    $pdf->SetXY($x, $y);
                    $tva = (float) $detail->getTva();
                    $pdf->MultiCell(20, 5, number_format($tva, 2, '.', '') . '%', 'T', 'C', 'C');
                    $tvaKey = (string) $tva;
                    $vatAmounts[$tvaKey] = ($vatAmounts[$tvaKey] ?? 0.0) + ($tva / 100) * $montantTtc;
                    $montantTtc *= 1 + ($tva / 100);
                    $x += 20;
                }

                $pdf->SetXY($x, $y);
                $unitPrice = $isSubjectedToVat
                    ? $this->formatValue($detail->getUnitPrice(), true)
                    : number_format($detail->getUnitPrice(), 2, '.', '');
                $pdf->MultiCell(30, 5, $unitPrice . $devise, 'T', 0, 'R');
                $x += 30;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(30, 5, $this->formatValue($montantTtc, $isSubjectedToVat) . $devise, 'T', 0, 'R');
            }

            $totalHt += $montantHt;
            $totalTtc += $montantTtc;
        }

        $pdf->Ln();

        if ($drawColumnLines) {
            foreach ($columns as $column) {
                $pdf->Line($pdf->GetX() + $column, $yInitial, $pdf->GetX() + $column, $pdf->GetY());
            }
        } elseif ($isSubjectedToVat) {
            $columns = [0, 30, 90, 110, 130, 160, 190];
            foreach ($columns as $column) {
                $pdf->Line($pdf->GetX() + $column, $yInitial, $pdf->GetX() + $column, $pdf->GetY());
            }
        }

        return [$totalHt, $totalTtc, $vatAmounts];
    }

    /**
     * @param array<string, float> $vatAmounts
     */
    private function renderTotals(PDF_Facture $pdf, bool $isSubjectedToVat, float $totalHt, float $totalTtc, array $vatAmounts, string $devise): void
    {
        if ($isSubjectedToVat) {
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(160, 5, 'TOTAL HT', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatValue($totalHt, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
            $pdf->Ln(5);

            foreach ($vatAmounts as $vat => $amount) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(160, 5, 'Total TVA ' . number_format((float) $vat, 2, '.', '') . '%', 1, 0, 'R', 1);
                $pdf->Cell(30, 5, $this->formatValue($amount, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
                $pdf->Ln(5);
            }
        }

        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL' . ($isSubjectedToVat ? ' TTC' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        $pdf->Cell(30, 5, $this->formatValue($totalTtc, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
    }

    private function formatValue(float $value, bool $isSubjectedToVat): string
    {
        if (!$isSubjectedToVat) {
            return (string) $value;
        }

        return number_format($value, 2, ',', ' ');
    }

    private function currencySymbol(Invoicing $invoicing): string
    {
        return $invoicing->getCurrency() === InvoicingCurrency::Dollar ? ' $' : ' €';
    }

    private function output(PDF_Facture $pdf, ?string $path, string $filename): string
    {
        if ($path === null) {
            return $pdf->Output('S', $filename, true);
        }

        $pdf->Output($path, 'F', true);
        return '';
    }
}
