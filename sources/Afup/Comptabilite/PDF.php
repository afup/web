<?php

namespace Afup\Site\Comptabilite;
use FPDF;

class PDF extends FPDF
{

    function Header()       //En-tête
    {
        $this->Ln(1);              //Saut de ligne
    }

//Pied de page
    function Footer()
    {
//    $this->SetY(-15);               //Positionnement à 1,5 cm du bas
//    $this->SetFont('Arial','I',8);  //Police Arial italique 8
//    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');      //Numéro de page

    }


    function tableau($position, $header, $data)
    {
        if ($position == 1) $position = 0; else $position = 150;
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
