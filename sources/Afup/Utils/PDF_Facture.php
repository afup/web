<?php

namespace Afup\Site\Utils;

use AppBundle\Compta\BankAccount\BankAccount;

class PDF_Facture extends \FPDF
{
    /**
     * The Afup\Site\Utils\Configuration object
     *
     * @var Configuration
     *
     * @access protected
     */
    public $configuration;

    /**
     * @var BankAccount
     */
    private $bankAccount;

    /**
     * Constructor
     *
     * @param Configuration $configuration The Afup\Site\Utils\Configuration object
     * @param BankAccount $bankAccount
     * @param string $orientation The page's orientation (portrait = P, landscape = L)
     * @param string $unit The page's units. Default is mm
     * @param string $format The page's format. Default is A4
     * @return void
     * @throws \Exception
     */
    function __construct($configuration, BankAccount $bankAccount, $orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        $this->bankAccount = $bankAccount;

        $this->setAFUPConfiguration($configuration);
    }

    /**
     * Set the Afup\Site\Utils\Configuration object
     *
     * @param Configuration $configuration The Afup\Site\Utils\Configuration object
     * @return void
     * @throws \Exception
     */
    function setAFUPConfiguration($configuration)
    {
        if (!is_object($configuration) || !($configuration instanceOf Configuration)) {
            throw new \Exception('$configuration parameter must be an instance of Afup\Site\Utils\AFUP_Configuration class');
        }

        $this->configuration = $configuration;
    }

    public function header()
    {
        // Haut de page [afup]
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(130, 5, 'AFUP');
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 5, $this->configuration->obtenir('afup|raison_sociale'));
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(130, 5, utf8_decode('Association Française des Utilisateurs de PHP'));
        $yFinAdresse = $this->GetY();


        $this->SetFont('Arial', '', 12);
        $this->MultiCell(60, 5, utf8_decode($this->configuration->obtenir('afup|adresse')));
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(130, 5, 'https://afup.org');
        $this->Ln();
        $this->Ln();
        $this->Cell(130, 5, 'SIRET : ' . $this->configuration->obtenir('afup|siret'));
        $this->SetFont('Arial', '', 12);
        $this->SetY($yFinAdresse);
        $this->Ln();
        $this->Cell(60, 5, $this->configuration->obtenir('afup|code_postal') . ' ' . utf8_decode($this->configuration->obtenir('afup|ville')));
        $this->Ln();
        $this->Cell(130, 5);
        $this->Cell(60, 5, 'Email : ' . $this->configuration->obtenir('afup|email'));

        $this->Ln();
        $this->Ln();
        $this->Ln();
    }

    /**
     * Returns the Afup\Site\Utils\Configuration object
     *
     * @return Configuration
     */
    function getAFUPConfiguration()
    {
        return $this->configuration;
    }

    function _putinfo()
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
     * @access public
     *
     * @see FPDF::Footer()
     */
    function Footer()
    {
        $address = sprintf(
            '%s - %u %s - %s - %s',
            $this->configuration->obtenir('afup|adresse'),
            $this->configuration->obtenir('afup|code_postal'),
            $this->configuration->obtenir('afup|ville'),
            $this->configuration->obtenir('afup|email'),
            'https://afup.org'
        );

        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(0, 3, utf8_decode('AFUP, Association Française des Utilisateurs de PHP'), 0, 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', '', 6);
        $this->Cell(0, 3, utf8_decode($address), 0, 0, 'C');
        $this->Ln();

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(170, 3, utf8_decode('N°') . ' SIRET', 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-140, 3, $this->configuration->obtenir('afup|siret'), 0, 0, 'C');
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
        $this->Cell(111, 3, utf8_decode('N° Cpte'), 0, 0, 'C');
        $this->SetFont('Arial', null, 6);
        $this->Cell(-88, 3, $this->bankAccount->getCompte(), 0, 0, 'C');

        $this->SetFont('Arial', 'B', 6);
        $this->Cell(107, 3, utf8_decode('Clé'), 0, 0, 'C');
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

        $this->Ln();

    }

}
