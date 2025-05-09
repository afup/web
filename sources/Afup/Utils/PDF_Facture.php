<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use Afup\Site\Comptabilite\PDF;
use AppBundle\Compta\BankAccount\BankAccount;

class PDF_Facture extends PDF
{
    /**
     * The Afup\Site\Utils\Configuration object
     *
     * @var Configuration
     *
     */
    public $configuration;

    /**
     * Constructor
     *
     * @param Configuration $configuration The Afup\Site\Utils\Configuration object
     * @param string $orientation The page's orientation (portrait = P, landscape = L)
     * @param string $unit The page's units. Default is mm
     * @param string $format The page's format. Default is A4
     * @return void
     * @throws \Exception
     * @param bool $isSubjectedToVat
     */
    public function __construct(
        $configuration,
        private readonly BankAccount $bankAccount,
        private $isSubjectedToVat = false,
        $orientation = 'P',
        $unit = 'mm',
        $format = 'A4',
    ) {
        parent::__construct($orientation, $unit, $format);

        $this->setAFUPConfiguration($configuration);
    }

    /**
     * Set the Afup\Site\Utils\Configuration object
     *
     * @param Configuration $configuration The Afup\Site\Utils\Configuration object
     * @throws \Exception
     */
    public function setAFUPConfiguration($configuration): void
    {
        if (!is_object($configuration) || !($configuration instanceof Configuration)) {
            throw new \Exception('$configuration parameter must be an instance of Afup\Site\Utils\AFUP_Configuration class');
        }

        $this->configuration = $configuration;
    }

    public function header(): void
    {
        // Haut de page [afup]
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(130, 5, 'AFUP');
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 5, AFUP_RAISON_SOCIALE);
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(130, 5, 'Association Française des Utilisateurs de PHP');
        $yFinAdresse = $this->GetY();


        $this->SetFont('Arial', '', 12);
        $this->MultiCell(60, 5, AFUP_ADRESSE);
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(130, 5, 'https://afup.org');
        $this->Ln();
        $this->Ln();
        $this->Cell(130, 5, 'SIRET : ' . AFUP_SIRET);
        $this->SetFont('Arial', '', 12);
        $this->SetY($yFinAdresse);
        $this->Ln();
        $this->Cell(60, 5, AFUP_CODE_POSTAL . ' ' . AFUP_VILLE);
        $this->Ln();
        $this->Cell(130, 5);
        $this->Cell(60, 5, 'Email : ' . AFUP_EMAIL);

        $this->Ln();
        $this->Ln();
        $this->Ln();
    }

    /**
     * Returns the Afup\Site\Utils\Configuration object
     *
     * @return Configuration
     */
    public function getAFUPConfiguration()
    {
        return $this->configuration;
    }

    public function _putinfo(): void
    {
        // on surcharge le _putinfo pour ne rien faire
        // cela permet entre-autres de ne pas indiquer en métadonnées la datetime de génération du PDF
        // ainsi les PDFs générées à des moments différents sont identiques, et donc comparables
    }

    /**
     * Overrides the parent Footer method
     *
     * Creates the PDF footer with RIB and IBAN informations
     *
     *
     * @see FPDF::Footer()
     */
    public function Footer(): void
    {
        $address = sprintf(
            '%s - %u %s - %s - %s',
            AFUP_ADRESSE,
            AFUP_CODE_POSTAL,
            AFUP_VILLE,
            AFUP_EMAIL,
            'https://afup.org'
        );

        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(0, 3, 'AFUP, Association Française des Utilisateurs de PHP', 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 6);
        $this->Cell(0, 3, $address, 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(170, 3, 'N°' . ' SIRET', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-140, 3, AFUP_SIRET, 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(130, 3, 'Identification RIB : Banque', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-95, 3, $this->bankAccount->getEtablissement(), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(112, 3, 'Guichet', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-95, 3, $this->bankAccount->getGuichet(), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(111, 3, 'N° Cpte', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-88, 3, $this->bankAccount->getCompte(), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(107, 3, 'Clé', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-99, 3, $this->bankAccount->getCle(), 0, 0, 'C');

        $this->Ln();

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(120, 3, 'Identification IBAN', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-62, 3, $this->bankAccount->getIban(), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(110, 3, 'BIC', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-90, 3, $this->bankAccount->getBic(), 0, 0, 'C');

        if ($this->isSubjectedToVat) {
            $this->Ln();

            $this->SetFont('Arial', 'B', 6);
            $this->Cell(155, 3, 'Numéro de TVA intracommunautaire', 0, 0, 'C');
            $this->SetFont('Arial', null, 6);
            $this->Cell(-60, 3, AFUP_NUMERO_TVA, 0, 0, 'C');
        }

        $this->Ln();
    }
}
