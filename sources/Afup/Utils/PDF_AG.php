<?php

namespace Afup\Site\Utils;

class PDF_AG extends \FPDF
{
    const CELL_HEIGHT = 7;

    private $footerTitle = '';

    public function prepareContent(array $personnesPhysiques)
    {
        $this->AddPage();

        $this->SetFont('Arial', 'B', 11);

        $this->writeRow([
            'Nom / Prénom',
            'Présence',
            'Pouvoir',
            'Émargement',
        ]);

        $this->Ln();

        foreach ($personnesPhysiques as $personne) {

            $this->SetFont('Arial', '', 12);

            $presence = '??';
            if ($personne['presence'] == AFUP_ASSEMBLEE_GENERALE_PRESENCE_OUI) {
                $presence = 'Présent';
            } elseif ($personne['presence'] == AFUP_ASSEMBLEE_GENERALE_PRESENCE_NON) {
                $presence = 'Absent';
            }

            $this->writeRow([
                $personne['nom'] . ' ' . $personne['prenom'],
                $presence,
                $personne['personnes_avec_pouvoir_nom'] . ' ' . $personne['personnes_avec_pouvoir_prenom'],
                '',
            ]);

            $this->Ln();
        }
    }

    private function writeRow(array $row)
    {
        $widths = [65, 20, 65, 35];

        foreach ($row as $pos => $value) {
            $this->Cell($widths[$pos], self::CELL_HEIGHT, utf8_decode($value), 1);
        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode($this->getFooterTitle()) . ' - Page '.$this->PageNo(),0,0,'C');
    }

    public function getFooterTitle()
    {
        return $this->footerTitle;
    }

    public function setFooterTitle($footerTitle)
    {
        $this->footerTitle = $footerTitle;
    }
}
