<?php

declare(strict_types=1);

namespace Afup\Site\Comptabilite;

use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;
use Afup\Site\Utils\Vat;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;

class Facture
{
    /**
     * @var Base_De_Donnees
     */
    private $_bdd;

    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }


    /* Journal des opération
     *
     */
    public function obtenirDevis($idPeriode = null)
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

        if (null !== $idPeriode) {
            $requete .= sprintf(' AND acf.date_devis >= (select date_debut from compta_periode where id = %s)', $this->_bdd->echapper($idPeriode));
            $requete .= sprintf(' AND acf.date_devis <= (select date_fin from compta_periode where id = %s)', $this->_bdd->echapper($idPeriode));
        }

        $requete .= 'GROUP BY ';
        $requete .= ' acf.id, date_devis, numero_devis, date_facture, numero_facture, societe, service, adresse, code_postal, ville, id_pays, email, observation, ref_clt1, ref_clt2, ref_clt3, nom, prenom, tel, etat_paiement, date_paiement, devise_facture ';
        $requete .= 'ORDER BY ';
        $requete .= ' acf.date_devis DESC';

        return $this->_bdd->obtenirTous($requete);
    }

    public function obtenirDevisDetails($id)
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

    public function obtenirFacture($idPeriode = null)
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

        if (null !== $idPeriode) {
            $requete .= sprintf(' AND acf.date_facture >= (select date_debut from compta_periode where id = %s)', $this->_bdd->echapper($idPeriode));
            $requete .= sprintf(' AND acf.date_facture <= (select date_fin from compta_periode where id = %s)', $this->_bdd->echapper($idPeriode));
        }

        $requete .= 'GROUP BY ';
        $requete .= ' acf.id, date_devis, numero_devis, date_facture, numero_facture, societe, service, adresse, code_postal, ville, id_pays, email, observation, ref_clt1, ref_clt2, ref_clt3, nom, prenom, tel, etat_paiement, date_paiement, devise_facture ';
        $requete .= 'ORDER BY ';
        $requete .= ' acf.date_facture DESC';

        return $this->_bdd->obtenirTous($requete);
    }

    public function obtenirFactureDetails($id)
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

    public function obtenir(string $id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function obtenirParNumeroFacture($numerofacture)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture ';
        $requete .= 'WHERE numero_facture = ' . $this->_bdd->echapper($numerofacture);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function obtenir_details(string $id)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture_details ';
        $requete .= 'WHERE idafup_compta_facture=' . $id;

        return $this->_bdd->obtenirTous($requete);
    }

    public function ajouter($date_devis, $societe, $service, $adresse, $code_postal, $ville, $id_pays,
                     $nom, $prenom, $tel, $email, $tva_intra, $observation, $ref_clt1, $ref_clt2, $ref_clt3,
                     $etat_paiement = 0, $date_paiement = null, $devise = 'EUR')
    {
        $requete = 'INSERT INTO ';
        $requete .= 'afup_compta_facture (';
        $requete .= 'date_devis,societe,service,adresse,code_postal,ville,id_pays,';
        $requete .= 'nom,prenom,tel,';
        $requete .= 'email,tva_intra,observation,ref_clt1,ref_clt2,ref_clt3,etat_paiement,date_paiement,numero_devis,devise_facture) ';
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
        $requete .= $this->_bdd->echapper($tva_intra) . ',';
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

    public function ajouter_details($ref, $designation, int $quantite, float $pu, int $tva = 0)
    {
        $requete = 'INSERT INTO ';
        $requete .= 'afup_compta_facture_details (';
        $requete .= 'idafup_compta_facture,ref,designation,quantite,pu,tva) ';
        $requete .= 'VALUES (';
        $requete .= $this->obtenirDernier() . ',';
        $requete .= $this->_bdd->echapper($ref) . ',';
        $requete .= $this->_bdd->echapper($designation) . ',';
        $requete .= $this->_bdd->echapper($quantite) . ',';
        $requete .= $this->_bdd->echapper($pu) . ',';
        $requete .= $this->_bdd->echapper($tva) . ' ';

        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    public function modifier($id, $date_devis, $societe, $service, $adresse, $code_postal, $ville, $id_pays,
                      $nom, $prenom, $tel, $email, $tva_intra, $observation, $ref_clt1, $ref_clt2, $ref_clt3,
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
        $requete .= 'tva_intra=' . $this->_bdd->echapper($tva_intra) . ',';
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

    public function modifier_details(string $id, $ref, $designation, int $quantite, float $pu, int $tva = 0)
    {
        $requete = 'UPDATE ';
        $requete .= 'afup_compta_facture_details ';
        $requete .= 'SET ';
        $requete .= 'ref=' . $this->_bdd->echapper($ref) . ',';
        $requete .= 'designation=' . $this->_bdd->echapper($designation) . ',';
        $requete .= 'quantite=' . $this->_bdd->echapper($quantite) . ',';
        $requete .= 'pu=' . $this->_bdd->echapper($pu) . ',';
        $requete .= 'tva=' . $this->_bdd->echapper($tva) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id=' . $id . ' ';

        return $this->_bdd->executer($requete);
    }

    public function obtenirDernier()
    {
        /**
         * @TODO ne supporte pas les enregistrements concurrents !
         */
        $requete = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  afup_compta_facture ';

        return $this->_bdd->obtenirUn($requete);
    }


    public function transfertDevis($numero_devis)
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

    public function genererNumeroFacture(): string
    {
        $year = (int) date('Y');

        $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1
            FROM afup_compta_facture
            WHERE LEFT(numero_facture, 4)=";
        $index = $this->_bdd->obtenirUn($sql . $year);

        // index null = changement d'année
        // on va chercher l'index de l'année dernière
        if (null === $index) {
            $index = $this->_bdd->obtenirUn($sql . ($year-1));
            $index = (int) (is_null($index) ? 1 : $index);
        }

        return "$year-$index";
    }

    public function genererNumeroDevis(): string
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


    public function genererDevis(string $reference, $chemin = null): void
    {
        $requete = 'SELECT * FROM afup_compta_facture WHERE numero_devis=' . $this->_bdd->echapper($reference);
        $coordonnees = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_compta_facture_details WHERE idafup_compta_facture=' . $this->_bdd->echapper($coordonnees['id']);
        $details = $this->_bdd->obtenirTous($requete);

        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        $dateDevis = isset($coordonnees['date_devis']) && !empty($coordonnees['date_devis'])
            ? \DateTimeImmutable::createFromFormat('Y-m-d', (string) $coordonnees['date_devis'])
            : new \DateTimeImmutable();

        $isSubjectedToVat = Vat::isSubjectedToVat($dateDevis);

        $bankAccountFactory = new BankAccountFactory();
        // Construction du PDF
        $pdf = new PDF_Facture($configuration, $bankAccountFactory->createApplyableAt($dateDevis), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateDevis->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, $coordonnees['societe'] . "\n" .
            $coordonnees['service'] . "\n" .
            $coordonnees['adresse'] . "\n" .
            $coordonnees['code_postal'] . " " .
            $coordonnees['ville'] . "\n" .
            $pays->obtenirNom($coordonnees['id_pays']) .
            ($coordonnees['tva_intra'] ? ("\n" . 'N° TVA Intracommunautaire : ' . $coordonnees['tva_intra']) : null)
        );

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, 'Devis n° ' . $reference, 0, 0, "C");
        $pdf->SetFont('Arial', '', 10);
        if ($coordonnees['ref_clt1'] || $coordonnees['ref_clt2'] || $coordonnees['ref_clt3']) {
            $pdf->Ln(15);
            $pdf->Cell(40, 5, 'Repère(s) : ');
        }

        if ($coordonnees['ref_clt1']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt1']);
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt2']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt2']);
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt3']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt3']);
            $pdf->Ln(5);
        }
        $pdf->Ln(10);

        $pdf->MultiCell(180, 5, "Comme convenu, nous vous prions de trouver votre devis");

        // Cadre
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

        $totalTtc = 0;
        $totalHt = 0;
        switch ($coordonnees['devise_facture']) {
            case 'DOL':
                $devise = ' $';
                break;
            case 'EUR':
            default:
                $devise = ' €';
                break;
        }

        $yInitial = $pdf->getY();

        $vatAmounts = [];

        foreach ($details as $detail) {
            if ($detail['quantite'] != 0) {
                $montantHt = $detail['quantite'] * $detail['pu'];
                $montantTtc = $montantHt;

                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);

                if ($isSubjectedToVat) {
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();

                    $pdf->MultiCell(30, 5, $detail['ref'], 'T');
                    $x += 30;
                    $pdf->SetXY($x, $y);

                    $pdf->MultiCell(60, 5, $detail['designation'], 'T');

                    $x += 60;
                    $pdf->SetXY($x, $y);
                    $pdf->MultiCell(20, 5, $detail['quantite'], 'T', 0, "C");

                    $x += 20;
                    $pdf->SetXY($x, $y);
                    $pdf->MultiCell(20, 5, $detail['tva'] . '%', 'T', 'C', "C");
                    if (!isset($vatAmounts[$detail['tva']])) {
                        $vatAmounts[$detail['tva']] = 0;
                    }
                    $vatAmounts[$detail['tva']] += ($detail['tva'] / 100) * $montantTtc;
                    $montantTtc *= 1 + ($detail['tva'] / 100);

                    $x += 20;
                    $pdf->SetXY($x, $y);

                    $pdf->MultiCell(30, 5, $this->formatFactureValue($detail['pu'], $isSubjectedToVat) . $devise, 'T', 0, "R");

                    $x += 30;
                    $pdf->SetXY($x, $y);
                    $pdf->MultiCell(30, 5, $this->formatFactureValue($montantTtc, $isSubjectedToVat) . $devise, 'T', 0, "R");
                } else {
                    $pdf->Cell(30, 5, $detail['ref'], 1);
                    $pdf->Cell(80, 5, $detail['designation'], 1);
                    $pdf->Cell(20, 5, $detail['quantite'], 1, 0, "C");
                    $pdf->Cell(30, 5, $detail['pu'] . $devise, 1, 0, "R");
                    $pdf->Cell(30, 5, $montantHt . $devise, 1, 0, "R");
                }

                $totalHt += $montantHt;
                $totalTtc += $montantTtc;
            }
        }

        if ($isSubjectedToVat) {
            $columns = [0, 30, 90, 110, 130, 160, 190];

            foreach ($columns as $column) {
                $pdf->Line($pdf->GetX() + $column, $yInitial, $pdf->GetX() + $column, $pdf->GetY());
            }

            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(160, 5, 'TOTAL HT', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatFactureValue($totalHt, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
            $pdf->Ln(5);

            foreach ($vatAmounts as $vat => $amount) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(160, 5, 'Total TVA ' . $vat . '%', 1, 0, 'R', 1);
                $pdf->Cell(30, 5, $this->formatFactureValue($amount, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
                $pdf->Ln(5);
            }
        } else {
            $pdf->ln();
        }

        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL' . ($isSubjectedToVat ? ' TTC' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        $pdf->Cell(30, 5, $this->formatFactureValue($totalTtc, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);

        $pdf->Ln(15);
        if (!$isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }
        $pdf->Ln(10);
        $pdf->Cell(10, 5, 'Observations : ');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(130, 5, $coordonnees['observation']);

        if (is_null($chemin)) {
            $pdf->Output('Devis - ' . $coordonnees['societe'] . ' - ' . $coordonnees['date_devis'] . '.pdf', 'D', true);
            exit(0);
        } else {
            $pdf->Output($chemin, 'F', true);
        }
    }


    public function genererFacture(string $reference, $chemin = null): void
    {
        $requete = 'SELECT * FROM afup_compta_facture WHERE numero_facture=' . $this->_bdd->echapper($reference);
        $coordonnees = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_compta_facture_details WHERE idafup_compta_facture=' . $this->_bdd->echapper($coordonnees['id']);
        $details = $this->_bdd->obtenirTous($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        $pays = new Pays($this->_bdd);

        $dateFacture = isset($coordonnees['date_facture']) && !empty($coordonnees['date_facture'])
            ? \DateTimeImmutable::createFromFormat('Y-m-d', (string) $coordonnees['date_facture'])
            : new \DateTimeImmutable();

        $bankAccountFactory = new BankAccountFactory();

        $isSubjectedToVat = Vat::isSubjectedToVat($dateFacture);

        // Construction du PDF
        $pdf = new PDF_Facture($configuration, $bankAccountFactory->createApplyableAt($dateFacture), $isSubjectedToVat);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . $dateFacture->format('d/m/Y'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // À l'attention du client [adresse]
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, $coordonnees['societe'] . "\n" .
            $coordonnees['service'] . "\n" .
            $coordonnees['adresse'] . "\n" .
            $coordonnees['code_postal'] . "\n" .
            $coordonnees['ville'] . "\n" .
            $pays->obtenirNom($coordonnees['id_pays']) .
            ($coordonnees['tva_intra'] ? ("\n" . 'N° TVA Intracommunautaire : ' . $coordonnees['tva_intra']) : null));

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, 'Facture n° ' . $reference, 0, 0, "C");
        $pdf->SetFont('Arial', '', 10);

        if ($coordonnees['ref_clt1'] || $coordonnees['ref_clt2'] || $coordonnees['ref_clt3']) {
            $pdf->Ln(15);
            $pdf->Cell(40, 5, 'Repère(s) : ');
        }
        if ($coordonnees['ref_clt1']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt1']);
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt2']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt2']);
            $pdf->Ln(5);
        }
        if ($coordonnees['ref_clt3']) {
            $pdf->setx(30);
            $pdf->Cell(100, 5, $coordonnees['ref_clt3']);
            $pdf->Ln(5);
        }
        $pdf->Ln(10);

        $pdf->MultiCell(180, 5, "Comme convenu, nous vous prions de trouver votre facture");
        // Cadre
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

        $totalTtc = 0;
        $totalHt = 0;
        switch ($coordonnees['devise_facture']) {
            case 'DOL':
                $devise = ' $';
                break;
            case 'EUR':
            default:
                $devise = ' €';
                break;
        }
        $yInitial = $pdf->getY();
        $columns = $isSubjectedToVat ? [0, 30, 90, 110, 130, 160, 190] : [0, 30, 110, 130, 160, 190];

        $vatAmounts = [];

        foreach ($details as $detail) {
            if ($detail['quantite'] != 0) {
                $montantHt = $detail['quantite'] * $detail['pu'];
                $montantTtc = $montantHt;

                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);

                $y = $pdf->GetY();
                $x = $pdf->GetX();

                $pdf->MultiCell(30, 5, $detail['ref'], 'T');
                $x += 30;
                $pdf->SetXY($x, $y);
                $designationLength = $isSubjectedToVat ? 60 : 80;
                $pdf->MultiCell($designationLength, 5, $detail['designation'], 'T');

                $x += $designationLength;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(20, 5, $detail['quantite'], 'T', 0, "C");

                if ($isSubjectedToVat) {
                    $x += 20;
                    $pdf->SetXY($x, $y);
                    $pdf->MultiCell(20, 5, $detail['tva'] . '%', 'T', 'C', "C");
                    if (!isset($vatAmounts[$detail['tva']])) {
                        $vatAmounts[$detail['tva']] = 0;
                    }
                    $vatAmounts[$detail['tva']] += ($detail['tva'] / 100) * $montantTtc;
                    $montantTtc *= 1 + ($detail['tva'] / 100);
                }

                $x += 20;
                $pdf->SetXY($x, $y);

                $pdf->MultiCell(30, 5, $this->formatFactureValue($detail['pu'], $isSubjectedToVat) . $devise, 'T', 0, "R");

                $x += 30;
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(30, 5, $this->formatFactureValue($montantTtc, $isSubjectedToVat) . $devise, 'T', 0, "R");

                $totalHt += $montantHt;
                $totalTtc += $montantTtc;
            }
        }

        $pdf->Ln();

        foreach ($columns as $column) {
            $pdf->Line($pdf->GetX() + $column, $yInitial, $pdf->GetX() + $column, $pdf->GetY());
        }

        if ($isSubjectedToVat) {
            $pdf->SetFillColor(225, 225, 225);
            $pdf->Cell(160, 5, 'TOTAL HT', 1, 0, 'R', 1);
            $pdf->Cell(30, 5, $this->formatFactureValue($totalHt, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
            $pdf->Ln(5);

            foreach ($vatAmounts as $vat => $amount) {
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(160, 5, 'Total TVA ' . $vat . '%', 1, 0, 'R', 1);
                $pdf->Cell(30, 5, $this->formatFactureValue($amount, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
                $pdf->Ln(5);
            }
        }

        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL' . ($isSubjectedToVat ? ' TTC' : ''), 1, 0, $isSubjectedToVat ? 'R' : 'L', 1);
        $pdf->Cell(30, 5, $this->formatFactureValue($totalTtc, $isSubjectedToVat) . $devise, 1, 0, 'R', 1);
        $pdf->Ln(15);

        if (!$isSubjectedToVat) {
            $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        }

        $pdf->Ln();
        $pdf->Cell(10, 5, 'Payable à réception');
        $pdf->Ln(10);
        if ($coordonnees['observation']) {
            $pdf->Cell(10, 5, 'Observations : ');
            $pdf->Ln(5);
            $pdf->SetFont('Arial', '', 8);
            $pdf->MultiCell(130, 5, $coordonnees['observation']);
        }

        if (is_null($chemin)) {
            $pdf->Output('Facture - ' . $coordonnees['societe'] . ' - ' . $coordonnees['date_facture'] . '.pdf', 'D', true);
            exit(0);
        } else {
            $pdf->Output($chemin, 'F', true);
        }
    }

    private function formatFactureValue($value, bool $isSubjectedToVat)
    {
        if (!$isSubjectedToVat) {
            return $value;
        }

        return number_format((float) $value, 2, ',', ' ');
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param   string $reference Reference de la facturation
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture(string $reference)
    {
        $personne = $this->obtenirParNumeroFacture($reference);

        $sujet = "Facture AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= AFUP_RAISON_SOCIALE . "\n";
        $corps .= AFUP_ADRESSE . "\n";
        $corps .= AFUP_CODE_POSTAL . " " . AFUP_VILLE . "\n";

        $chemin_facture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $reference . '.pdf';
        $this->genererFacture($reference, $chemin_facture);

        $message = new Message($sujet, new MailUser(MailUser::DEFAULT_SENDER_EMAIL, MailUser::DEFAULT_SENDER_NAME), new MailUser($personne['email'], $personne['nom']));
        $message->addAttachment(new Attachment(
            $chemin_facture,
            'facture-' . $reference . '.pdf',
            'base64',
            'application/pdf'
        ));
        $ok = Mailing::envoyerMail($message, $corps);

        @unlink($chemin_facture);

        return $ok;
    }
}
