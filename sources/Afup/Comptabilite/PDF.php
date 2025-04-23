<?php

declare(strict_types=1);

namespace Afup\Site\Comptabilite;

use tFPDF;

class PDF extends tFPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);

        define('_SYSTEM_TTFONTS', __DIR__ . '/../../../assets/fonts/');
        $this->AddFont('Arial','','Arial.ttf',true);
        $this->AddFont('Arial','B','Arial_Bold.ttf',true);
        $this->AddFont('Arial','BI','Arial_Bold_Italic.ttf',true);
        $this->AddFont('Arial','I','Arial_Italic.ttf',true);
    }

    public function Header(): void       //En-tête
    {
        $this->Ln(1);              //Saut de ligne
    }

    //Pied de page
    public function Footer(): void
    {
//    $this->SetY(-15);               //Positionnement à 1,5 cm du bas
//    $this->SetFont('Arial','I',8);  //Police Arial italique 8
//    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');      //Numéro de page
    }


    public function tableau($position, $header, $data): void
    {
        $position = $position == 1 ? 0 : 150;
        $y = 30;

        //Couleurs, épaisseur du trait et police grasse
        $this->SetFillColor(128, 128, 128);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(250, 250, 250);
        $this->SetLineWidth(.2);
        $this->SetFont('Times', 'B', 10);
        //En-tête
//    $w=array(40,35,45,40);
        $w = [30, 85, 20];

        //  Categorie","Description","Montant
        $this->SETXY($position, $y);
        $y += 5;
        $this->Cell($w[0], 6, 'Categorie', 'LR', 0, 'C');
        $this->Cell($w[1], 6, 'Description', 'LR', 0, 'C');
        $this->Cell($w[2], 6, 'Montant', 'LR', 0, 'C');


        $this->Ln();

        //Restauration des couleurs et de la police
        $this->SetFillColor(224, 224, 224);
        $this->SetTextColor(0);
        $this->SetFont('Times', '', 10);
        //Données
        $fill = false;

        foreach ($data as $row) {
            $this->SETXY($position, $y);
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);

            $this->Cell($w[1], 6, substr($row[1], 0, 54), 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, number_format($row[2], 2, ',', ' '), 'LR', 0, 'R', $fill);

            $this->Ln();
            $fill = !$fill;
            $y += 5;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
