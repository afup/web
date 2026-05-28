<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee;

use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Accounting\Invoices\InvoiceGenerator;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\MembershipFee\Model\Repository\MembershipFeeRepository;

final readonly class MembershipFeeInvoicePdfGenerator
{
    public function __construct(
        private MembershipFeeRepository $membershipFeeRepository,
        private UserRepository $userRepository,
        private CompanyMemberRepository $companyMemberRepository,
        private InvoiceGenerator $invoiceGenerator,
        private BankAccountFactory $bankAccountFactory,
    ) {}

    /**
     * Génère une facture au format PDF
     *
     * @param $idCotisation Identifiant de la cotisation
     * @param $chemin Chemin du fichier PDF à générer. Si ce chemin est omis, le PDF est renvoyé au navigateur.
     * @return string|null Le numéro de la facture
     */
    public function genererFacture(int $idCotisation, ?string $chemin = null): ?string
    {
        $cotisation = $this->membershipFeeRepository->get($idCotisation);

        $userRepository = match ($cotisation->getUserType()) {
            MemberType::MemberCompany => $this->companyMemberRepository,
            default => $this->userRepository,
        };

        /** @var User|CompanyMember $user */
        $user = $userRepository->get($cotisation->getUserId());
        $invoiceData = $this->invoiceGenerator->getInvoiceData($user);

        $dateFacture = $cotisation->getInvoiceDate()
            ?? \DateTimeImmutable::createFromFormat('U', (string) $cotisation->getStartDate()->getTimestamp());

        $isSubjectedToVat = Vat::isSubjectedToVat($dateFacture);
        $pdf = new PDF_Facture($this->bankAccountFactory->createApplyableAt($dateFacture), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, 'Objet : Facture n°' . $cotisation->getInvoiceNumber());
        $pdf->SetFont('Arial', '', 10);

        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, $invoiceData->recipient . "\n" . $invoiceData->address . "\n" . $invoiceData->zipcode . "\n" . $invoiceData->city);

        if ($cotisation->getClientReference() !== null) {
            $pdf->Ln(10);
            $pdf->MultiCell(180, 5, sprintf(
                "Référence client : %s",
                $cotisation->getClientReference(),
            ));
        }

        $pdf->Ln(15);
        $pdf->MultiCell(180, 5, "Facture concernant votre adhésion à l'Association Française des Utilisateurs de PHP (AFUP).");

        $dateFin = $cotisation->getEndDate()->getTimestamp();
        $montant = $cotisation->getAmount();

        if (false === $isSubjectedToVat) {
            $pdf->Ln(10);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(50, 5, 'Code', 1, 0, 'L', 1);
            $pdf->Cell(100, 5, 'Désignation', 1, 0, 'L', 1);
            $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(50, 5, 'ADH', 1);
            $pdf->Cell(100, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', $dateFin), 1);
            $pdf->Cell(40, 5, number_format($montant, 2, '.', '') . ' €', 1);

            $pdf->Ln(15);
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        } else {
            // On stocke le montant de la cotisation TTC. Pour les personnes morales, on extrait le HT pour éviter d'appliquer deux fois la TVA.
            if ($cotisation->getUserType() === MemberType::MemberCompany) {
                $montant = Vat::getRoundedWithoutVatPriceFromPriceWithVat($montant, Utils::MEMBERSHIP_FEE_VAT_RATE);
            }

            $pdf->Ln(10);
            $pdf->SetFillColor(200, 200, 200);
            $pdf->Cell(20, 5, 'Code', 1, 0, 'L', 1);
            $pdf->Cell(95, 5, 'Désignation', 1, 0, 'L', 1);
            $pdf->Cell(25, 5, 'Prix HT', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, 'Taux TVA', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, 'Prix TTC', 1, 0, 'R', 1);

            if ($cotisation->getUserType() === MemberType::MemberCompany) {
                [$totalHt, $total] = $this->buildDetailsPersonneMorale($pdf, $montant, $dateFin);
            } else {
                [$totalHt, $total] = $this->buildDetailsPersonnePhysique($pdf, $montant, $dateFin);
            }

            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(165, 5, 'Total HT', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($totalHt) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(165, 5, 'Total TVA 20%', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($total - $totalHt) . ' €', 1, 0, 'R', 1);

            $pdf->Ln();
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(165, 5, 'Total TTC', 1, 0, 'R', 1);
            $pdf->Cell(25, 5, $this->formatFactureValue($total) . ' €', 1, 0, 'R', 1);
        }

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'Lors de votre règlement, merci de préciser la mention : "Facture n°' . $cotisation->getInvoiceNumber() . '"');

        if (is_null($chemin)) {
            $pattern = str_replace(' ', '', $invoiceData->patternPrefix) . '_' . $cotisation->getInvoiceNumber() . '_' . date('dmY', $cotisation->getStartDate()->getTimestamp()) . '.pdf';
            $pdf->Output($pattern, 'D', true);
        } else {
            $pdf->Output($chemin, 'F', true);
        }

        return $cotisation->getInvoiceNumber();
    }

    private function buildDetailsPersonneMorale(PDF_Facture $pdf, float $montant, int $dateFin): array
    {
        $montantTtc = $montant * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE);
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', $dateFin), 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montant) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, (Utils::MEMBERSHIP_FEE_VAT_RATE * 100 . ' %'), 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantTtc) . ' €', 1, 0, 'R');

        return [$montant, $montantTtc];
    }

    private function buildDetailsPersonnePhysique(PDF_Facture $pdf, float $montant, int $dateFin): array
    {
        $montantFixeHt = 5 / 100 * $montant;
        $montantFixeTtc = $montantFixeHt * (1 + Utils::MEMBERSHIP_FEE_VAT_RATE);
        $montantVariable = $montant - $montantFixeTtc;

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH-var', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', $dateFin) . ' - part variable', 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montantFixeHt) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, (Utils::MEMBERSHIP_FEE_VAT_RATE * 100 . ' %'), 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantFixeTtc) . ' €', 1, 0, 'R');

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(20, 5, 'ADH-fixe', 1);
        $pdf->Cell(95, 5, "Adhésion AFUP jusqu'au " . date('d/m/Y', $dateFin) . ' - part fixe', 1);
        $pdf->Cell(25, 5, $this->formatFactureValue($montantVariable) . ' €', 1, 0, 'R');
        $pdf->Cell(25, 5, '0 %', 1, 0, 'R');
        $pdf->Cell(25, 5, $this->formatFactureValue($montantVariable) . ' €', 1, 0, 'R');

        return [$montantFixeHt + $montantVariable, $montantFixeTtc + $montantVariable];
    }

    private function formatFactureValue(float $value): string
    {
        return number_format($value, 2, ',', ' ');
    }
}
