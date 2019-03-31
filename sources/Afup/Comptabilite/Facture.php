<?php


namespace Afup\Site\Comptabilite;

use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;

class Facture
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    var $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }


    /* Journal des opération
     *
     */
    function obtenirDevis()
    {

        $requete = 'SELECT ';
        $requete .= ' acf.*, sum(quantite * pu) prix ';
        $requete .= 'FROM ';
        $requete .= ' afup_compta_facture acf ';
        $requete .= 'LEFT JOIN  ';
        $requete .= ' afup_compta_facture_details acfd ';
        $requete .= 'ON ';
        $requete .= ' acfd.idafup_compta_facture = acf.id ';
        $requete .= 'WHERE  ';
        $requete .= ' numero_devis != "" ';
        $requete .= 'GROUP BY ';
        $requete .= ' acf.id, date_devis, numero_devis, date_facture, numero_facture, societe, service, adresse, code_postal, ville, id_pays, email, observation, ref_clt1, ref_clt2, ref_clt3, nom, prenom, tel, etat_paiement, date_paiement, devise_facture ';
        $requete .= 'ORDER BY ';
        $requete .= ' acf.date_devis DESC';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirDevisDetails($id)
    {

        $requete = 'SELECT ';
        $requete .= 'afup_compta_facture.*, ';
        $requete .= 'afup_compta_facture_details.ref,afup_compta_facture_details.designation,afup_compta_facture_details.quantite,afup_compta_facture_details.pu ';
        $requete .= 'FROM  ';
        $requete .= 'afup_compta_facture,  ';
        $requete .= 'afup_compta_facture_details ';
        $requete .= 'WHERE  ';
        $requete .= ' numero_devis != "" ';
        $requete .= 'afup_compta_facture.id = afup_compta_facture_details.idafup_compta_facture ';
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_devis ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirFacture()
    {

        $requete = 'SELECT ';
        $requete .= ' acf.*, sum(quantite * pu) prix ';
        $requete .= 'FROM ';
        $requete .= ' afup_compta_facture acf ';
        $requete .= 'LEFT JOIN  ';
        $requete .= ' afup_compta_facture_details acfd ';
        $requete .= 'ON ';
        $requete .= ' acfd.idafup_compta_facture = acf.id ';
        $requete .= 'WHERE  ';
        $requete .= ' numero_facture != "" ';
        $requete .= 'GROUP BY ';
        $requete .= ' acf.id, date_devis, numero_devis, date_facture, numero_facture, societe, service, adresse, code_postal, ville, id_pays, email, observation, ref_clt1, ref_clt2, ref_clt3, nom, prenom, tel, etat_paiement, date_paiement, devise_facture ';
        $requete .= 'ORDER BY ';
        $requete .= ' acf.date_facture DESC';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirFactureDetails($id)
    {

        $requete = 'SELECT ';
        $requete .= 'afup_compta_facture.*, ';
        $requete .= 'afup_compta_facture_details.ref,afup_compta_facture_details.designation,afup_compta_facture_details.quantite,afup_compta_facture_details.pu ';
        $requete .= 'FROM  ';
        $requete .= 'afup_compta_facture,  ';
        $requete .= 'afup_compta_facture_details ';
        $requete .= 'WHERE  ';
        $requete .= ' numero_facture != "" ';
        $requete .= 'afup_compta_facture.id = afup_compta_facture_details.idafup_compta_facture ';
        $requete .= 'ORDER BY ';
        $requete .= 'compta.date_facture ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenir($id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenir_details($id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture_details ';
        $requete .= 'WHERE idafup_compta_facture=' . $id;

        return $this->_bdd->obtenirTous($requete);
    }

    function ajouter($date_devis, $societe, $service, $adresse, $code_postal, $ville, $id_pays,
                     $nom, $prenom, $tel, $email, $observation, $ref_clt1, $ref_clt2, $ref_clt3,
                     $etat_paiement = 0, $date_paiement = null, $devise = 'EUR')
    {

        $requete = 'INSERT INTO ';
        $requete .= 'afup_compta_facture (';
        $requete .= 'date_devis,societe,service,adresse,code_postal,ville,id_pays,';
        $requete .= 'nom,prenom,tel,';
        $requete .= 'email,observation,ref_clt1,ref_clt2,ref_clt3,etat_paiement,date_paiement,numero_devis,devise_facture) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($date_devis) . ',';
        $requete .= $this->_bdd->echapper($societe) . ',';
        $requete .= $this->_bdd->echapper($service) . ',';
        $requete .= $this->_bdd->echapper($adresse) . ',';
        $requete .= $this->_bdd->echapper($code_postal) . ',';
        $requete .= $this->_bdd->echapper($ville) . ',';
        $requete .= $this->_bdd->echapper($id_pays) . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= $this->_bdd->echapper($prenom) . ',';
        $requete .= $this->_bdd->echapper($tel) . ',';
        $requete .= $this->_bdd->echapper($email) . ',';
        $requete .= $this->_bdd->echapper($observation) . ',';
        $requete .= $this->_bdd->echapper($ref_clt1) . ',';
        $requete .= $this->_bdd->echapper($ref_clt2) . ',';
        $requete .= $this->_bdd->echapper($ref_clt3) . ', ';
        $requete .= $this->_bdd->echapper($etat_paiement) . ', ';
        $requete .= $this->_bdd->echapper($date_paiement) . ', ';
        $requete .= $this->_bdd->echapper($this->genererNumeroDevis()) . ', ';
        $requete .= $this->_bdd->echapper($devise) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    function ajouter_details($ref, $designation, $quantite, $pu)
    {
        $requete = 'INSERT INTO ';
        $requete .= 'afup_compta_facture_details (';
        $requete .= 'idafup_compta_facture,ref,designation,quantite,pu) ';
        $requete .= 'VALUES (';
        $requete .= $this->obtenirDernier() . ',';
        $requete .= $this->_bdd->echapper($ref) . ',';
        $requete .= $this->_bdd->echapper($designation) . ',';
        $requete .= $this->_bdd->echapper($quantite) . ',';
        $requete .= $this->_bdd->echapper($pu) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    function modifier($id, $date_devis, $societe, $service, $adresse, $code_postal, $ville, $id_pays,
                      $nom, $prenom, $tel, $email, $observation, $ref_clt1, $ref_clt2, $ref_clt3,
                      $numero_devis, $numero_facture, $etat_paiement, $date_paiement, $devise)
    {

        $requete = 'UPDATE ';
        $requete .= 'afup_compta_facture ';
        $requete .= 'SET ';
        $requete .= 'date_devis=' . $this->_bdd->echapper($date_devis) . ',';
        $requete .= 'societe=' . $this->_bdd->echapper($societe) . ',';
        $requete .= 'service=' . $this->_bdd->echapper($service) . ',';
        $requete .= 'adresse=' . $this->_bdd->echapper($adresse) . ',';
        $requete .= 'code_postal=' . $this->_bdd->echapper($code_postal) . ',';
        $requete .= 'ville=' . $this->_bdd->echapper($ville) . ',';
        $requete .= 'id_pays=' . $this->_bdd->echapper($id_pays) . ',';
        $requete .= 'nom=' . $this->_bdd->echapper($nom) . ',';
        $requete .= 'prenom=' . $this->_bdd->echapper($prenom) . ',';
        $requete .= 'tel=' . $this->_bdd->echapper($tel) . ',';
        $requete .= 'email=' . $this->_bdd->echapper($email) . ',';
        $requete .= 'observation=' . $this->_bdd->echapper($observation) . ', ';
        $requete .= 'ref_clt1=' . $this->_bdd->echapper($ref_clt1) . ',';
        $requete .= 'ref_clt2=' . $this->_bdd->echapper($ref_clt2) . ',';
        $requete .= 'ref_clt3=' . $this->_bdd->echapper($ref_clt3) . ', ';
        $requete .= 'etat_paiement=' . $this->_bdd->echapper($etat_paiement) . ', ';
        $requete .= 'date_paiement=' . $this->_bdd->echapper($date_paiement) . ', ';
        $requete .= 'numero_devis=' . $this->_bdd->echapper($numero_devis) . ', ';
        $requete .= 'devise_facture=' . $this->_bdd->echapper($devise) . ' ';

        if ($numero_facture) {
            $requete .= ', ';
            $requete .= 'numero_facture=' . $this->_bdd->echapper($numero_facture) . ' ';
        }
        $requete .= 'WHERE ';
        $requete .= 'id=' . $this->_bdd->echapper($id) . ' ';

        return $this->_bdd->executer($requete);
    }

    function modifier_details($id, $ref, $designation, $quantite, $pu)
    {
        $requete = 'UPDATE ';
        $requete .= 'afup_compta_facture_details ';
        $requete .= 'SET ';
        $requete .= 'ref=' . $this->_bdd->echapper($ref) . ',';
        $requete .= 'designation=' . $this->_bdd->echapper($designation) . ',';
        $requete .= 'quantite=' . $this->_bdd->echapper($quantite) . ',';
        $requete .= 'pu=' . $this->_bdd->echapper($pu) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id=' . $id . ' ';

        return $this->_bdd->executer($requete);
    }

    function obtenirDernier()
    {
        /**
         * @TODO ne supporte pas les enregistrements concurrents !
         */
        $requete = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture ';

        return $this->_bdd->obtenirUn($requete);
    }


    function transfertDevis($numero_devis)
    {
        $numero_facture = $this->genererNumeroFacture();

        $requete = 'UPDATE ';
        $requete .= 'afup_compta_facture ';
        $requete .= 'SET ';
        $requete .= 'date_facture=' . $this->_bdd->echapper(date('Y-m-d')) . ',';
        $requete .= 'numero_facture=' . $this->_bdd->echapper($numero_facture) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'numero_devis=' . $this->_bdd->echapper($numero_devis) . ' ';

        return $this->_bdd->executer($requete);
    }

    function genererNumeroFacture()
    {
        // afup_cotisations
        $requete = 'SELECT';
        $requete .= "  MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 ";
        $requete .= 'FROM';
        $requete .= ' afup_compta_facture ';
        $requete .= 'WHERE';
        $requete .= '  LEFT(numero_facture, 4)=' . $this->_bdd->echapper(date('Y'));
        $index = $this->_bdd->obtenirUn($requete);
        return date('Y') . '-' . (is_null($index) ? 1 : $index);
    }

    function genererNumeroDevis()
    {
        $requete = 'SELECT';
        $requete .= "  MAX(CAST(SUBSTRING_INDEX(numero_devis, '-', -1) AS UNSIGNED)) + 1 ";
        $requete .= 'FROM';
        $requete .= ' afup_compta_facture ';
        $requete .= 'WHERE';
        $requete .= '  LEFT(numero_devis, 4)=' . $this->_bdd->echapper(date('Y'));

        $index = $this->_bdd->obtenirUn($requete);
        return date('Y') . '-' . sprintf('%02d', (is_null($index) ? 1 : $index));
    }


    function genererDevis($reference, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_compta_facture WHERE numero_devis=' . $this->_bdd->echapper($reference);
        $coordonnees = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_compta_facture_details WHERE idafup_compta_facture=' . $this->_bdd->echapper($coordonnees['id']);
        $details = $this->_bdd->obtenirTous($requete);

        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        // Construction du PDF
        $pdf = new PDF_Facture($configuration);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($coordonnees['date_devis']) && !empty($coordonnees['date_devis'])) ? strtotime($coordonnees['date_devis']) : time()));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, utf8_decode($coordonnees['societe']) . "\n" .
            utf8_decode($coordonnees['service']) . "\n" .
            utf8_decode($coordonnees['adresse']) . "\n" .
            utf8_decode($coordonnees['code_postal']) . " " .
            utf8_decode($coordonnees['ville']) . "\n" .
            utf8_decode($pays->obtenirNom($coordonnees['id_pays'])));

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, utf8_decode('Devis n° ' . $reference), 0, 0, "C");
        $pdf->SetFont('Arial', '', 10);
        if ($coordonnees['ref_clt1'] || $coordonnees['ref_clt2'] || $coordonnees['ref_clt3']) {
            $pdf->Ln(15);
            $pdf->Cell(40, 5, utf8_decode('Repère(s) : '));
        }

        if ($coordonnees['ref_clt1']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt1']));
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt2']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt2']));
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt3']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt3']));
            $pdf->Ln(5);
        }
        $pdf->Ln(10);

        $pdf->MultiCell(180, 5, utf8_decode("Comme convenu, nous vous prions de trouver votre devis"));

        // Cadre
        $pdf->Ln(5);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(30, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(80, 5, 'Description', 1, 0, 'L', 1);
        $pdf->Cell(20, 5, 'Quantite', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Prix', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Total', 1, 0, 'L', 1);

        $total = 0;
        switch ($coordonnees['devise_facture']) {
            case 'DOL':
                $devise = ' $';
                break;
            case 'EUR':
            default:
                $devise = utf8_decode(' ');
                break;
        }
        foreach ($details as $detail) {
            if ($detail['quantite'] != 0) {
                $montant = $detail['quantite'] * $detail['pu'];

                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);

                $pdf->Cell(30, 5, $detail['ref'], 1);
                $pdf->Cell(80, 5, utf8_decode($detail['designation']), 1);
                $pdf->Cell(20, 5, utf8_decode($detail['quantite']), 1, 0, "C");
                $pdf->Cell(30, 5, utf8_decode($detail['pu']) . $devise, 1, 0, "R");
                $pdf->Cell(30, 5, utf8_decode($montant) . $devise, 1, 0, "R");

                $total += $montant;

            }
        }
        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, $total . $devise, 1, 0, 'R', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        $pdf->Ln(10);
        $pdf->Cell(10, 5, 'Observations : ');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(130, 5, utf8_decode($coordonnees['observation']));

        if (is_null($chemin)) {
            $pdf->Output('Devis - ' . $coordonnees['societe'] . ' - ' . $coordonnees['date_devis'] . '.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }
    }


    function genererFacture($reference, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_compta_facture WHERE numero_facture=' . $this->_bdd->echapper($reference);
        $coordonnees = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_compta_facture_details WHERE idafup_compta_facture=' . $this->_bdd->echapper($coordonnees['id']);
        $details = $this->_bdd->obtenirTous($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        // Construction du PDF

        $pdf = new PDF_Facture($configuration);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($coordonnees['date_facture']) && !empty($coordonnees['date_facture'])) ? strtotime($coordonnees['date_facture']) : time()));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, utf8_decode($coordonnees['societe']) . "\n" .
            utf8_decode($coordonnees['service']) . "\n" .
            utf8_decode($coordonnees['adresse']) . "\n" .
            utf8_decode($coordonnees['code_postal']) . "\n" .
            utf8_decode($coordonnees['ville']) . "\n" .
            utf8_decode($pays->obtenirNom($coordonnees['id_pays'])));

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, utf8_decode('Facture n° ' . $reference), 0, 0, "C");
        $pdf->SetFont('Arial', '', 10);

        if ($coordonnees['ref_clt1'] || $coordonnees['ref_clt2'] || $coordonnees['ref_clt3']) {
            $pdf->Ln(15);
            $pdf->Cell(40, 5, utf8_decode('Repère(s) : '));
        }
        if ($coordonnees['ref_clt1']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt1']));
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt2']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt2']));
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt3']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, utf8_decode($coordonnees['ref_clt3']));
            $pdf->Ln(5);
        }
        $pdf->Ln(10);

        $pdf->MultiCell(180, 5, utf8_decode("Comme convenu, nous vous prions de trouver votre facture"));
        // Cadre
        $pdf->Ln(5);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(30, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(80, 5, 'Description', 1, 0, 'L', 1);
        $pdf->Cell(20, 5, 'Quantite', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Prix', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Total', 1, 0, 'L', 1);

        $total = 0;
        switch ($coordonnees['devise_facture']) {
            case 'DOL':
                $devise = ' $';
                break;
            case 'EUR':
            default:
                $devise = utf8_decode(' ');
                break;
        }
        $yInitial = $pdf->getY();
        $columns = [0, 30, 110, 130, 160, 190];
        foreach ($details as $detail) {
            if ($detail['quantite'] != 0) {
                $montant = $detail['quantite'] * $detail['pu'];

                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);

                $y = $pdf->GetY();
                $x = $pdf->GetX();

                $pdf->MultiCell(30, 5, $detail['ref'], 'T');
                $x += 30;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(80, 5, utf8_decode($detail['designation']), 'T');

                $x += 80;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(20, 5, utf8_decode($detail['quantite']), 'T', 0, "C");

                $x += 20;
                $pdf->SetXY($x, $y);

                $pdf->MultiCell(30, 5, utf8_decode($detail['pu']) . $devise, 'T', 0, "R");

                $x += 30;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(30, 5, utf8_decode($montant) . $devise, 'T', 0, "R");

                $total += $montant;
            }
        }

        $pdf->Ln();

        foreach ($columns as $column) {
            $pdf->Line($pdf->GetX() + $column, $yInitial, $pdf->GetX() + $column, $pdf->GetY());
        }

        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, $total . $devise, 1, 0, 'R', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        $pdf->Ln();
        $pdf->Cell(10, 5, utf8_decode('Payable à réception'));
        $pdf->Ln(10);
        if ($coordonnees['observation']) {
            $pdf->Cell(10, 5, 'Observations : ');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 8);
            $pdf->MultiCell(130, 5, utf8_decode($coordonnees['observation']));
        }

        if (is_null($chemin)) {
            $pdf->Output('Facture - ' . $coordonnees['societe'] . ' - ' . $coordonnees['date_facture'] . '.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }

    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param   string $reference Reference de la facturation
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerFacture($reference)
    {

        $configuration = $GLOBALS['AFUP_CONF'];

        $personne = $this->obtenir($reference, 'email, nom, prenom');

        $sujet = "Facture AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale') . "\n";
        $corps .= $configuration->obtenir('afup|adresse') . "\n";
        $corps .= $configuration->obtenir('afup|code_postal') . " " . $configuration->obtenir('afup|ville') . "\n";

        $chemin_facture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $reference . '.pdf';
        $this->genererFacture($reference, $chemin_facture);

        $ok = Mailing::envoyerMail(
            $GLOBALS['conf']->obtenir('mails|email_expediteur'),
            array($personne['email'], $personne['nom']),
            $sujet,
            $corps,
            array('file' => array($chemin_facture, 'facture-' . $reference . '.pdf'))
        );

        @unlink($chemin_facture);

        return $ok;
    }
}
