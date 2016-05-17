<?php
namespace Afup\Site\Forum;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Mail;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\PDF_Facture;

require_once 'Afup/Inscriptions.php';

class Facturation
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une inscription
     *
     * @param  string $reference Reference de la facturation
     * @param  string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($reference, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoit la liste des inscriptions à facturer ou facturé au forum
     *
     * @param  int $id_forum Id du forum
     * @param  string $champs Champs à renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($id_forum = null,
                          $champs = '*',
                          $ordre = 'date_reglement',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'WHERE etat IN ( ' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND id_forum =' . $id_forum . ' ';
        if ($filtre) {
            $requete .= '  AND societe LIKE \'%' . $filtre . '%\') ';
        }
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function creerReference($id_forum, $label)
    {
        $label = preg_replace('/[^A-Z0-9_\-\:\.;]/', '', strtoupper(supprimerAccents($label)));

        return 'F' . date('Y') . sprintf('%02d', $id_forum) . '-' . date('dm') . '-' . substr($label, 0, 5) . '-' . substr(md5(date('r') . $label), -5);
    }

    function gererFacturation($reference, $type_reglement, $informations_reglement, $date_reglement, $email,
                              $societe, $nom, $prenom, $adresse, $code_postal, $ville, $id_pays, $id_forum, $old_reference,
                              $autorisation = null, $transaction = null,
                              $etat = AFUP_FORUM_ETAT_CREE, $facturation = AFUP_FORUM_FACTURE_A_ENVOYER)
    {
        $ok = false;
        $nb = $this->_getNbInscriptions($reference);
        $facturation = (bool)$this->obtenir($old_reference, 'reference');
        $old_reference = (!isset($old_reference) ? '' : $old_reference);

        // Si la reference n'existe pas on l'ajoute sinon on la met à jour...
        if ($nb == 0 || ($nb == 1 && empty($old_reference)) || !$facturation) {
            $ok = $this->_ajouterFacturation($reference, $type_reglement, $informations_reglement, $date_reglement, $email,
                $societe, $nom, $prenom, $adresse, $code_postal, $ville, $id_pays, $id_forum,
                $autorisation, $transaction, $etat, $facturation);
        } else {
            $ok = $this->_modifierFacturation($reference, $type_reglement, $informations_reglement, $date_reglement, $email,
                $societe, $nom, $prenom, $adresse, $code_postal, $ville, $id_pays, $id_forum,
                $autorisation, $transaction, $etat, $facturation);
        }

        // Si on change de reference
        if ($old_reference != $reference && !empty($old_reference)) {
            $ok &= $this->supprimerFacturation($old_reference);
        }

        return $ok;
    }

    function _getNbInscriptions($reference)
    {
        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->obtenirUn($requete);
    }

    function _ajouterFacturation($reference, $type_reglement, $informations_reglement, $date_reglement, $email,
                                 $societe, $nom, $prenom, $adresse, $code_postal, $ville, $id_pays, $id_forum,
                                 $autorisation = null, $transaction = null, $etat = AFUP_FORUM_ETAT_CREE)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_facturation_forum (reference, montant, type_reglement, informations_reglement, date_reglement,
                               societe, nom, prenom, email, adresse, code_postal, ville, id_pays, autorisation, transaction, etat, id_forum) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($reference) . ',';
        $requete .= '(SELECT IFNULL(SUM(montant), 0.0) FROM afup_inscription_forum WHERE reference = ' . $this->_bdd->echapper($reference) . '),';
        $requete .= $this->_bdd->echapper($type_reglement) . ',';
        $requete .= $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= $this->_bdd->echapper($date_reglement) . ',';
        $requete .= $this->_bdd->echapper($societe) . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= $this->_bdd->echapper($prenom) . ',';
        $requete .= $this->_bdd->echapper($email) . ',';
        $requete .= $this->_bdd->echapper($adresse) . ',';
        $requete .= $this->_bdd->echapper($code_postal) . ',';
        $requete .= $this->_bdd->echapper($ville) . ',';
        $requete .= $this->_bdd->echapper($id_pays) . ',';
        $requete .= $this->_bdd->echapper($autorisation) . ',';
        $requete .= $this->_bdd->echapper($transaction) . ',';
        $requete .= $etat . ',';
        $requete .= $id_forum . ')';

        return $this->_bdd->executer($requete);
    }

    function _modifierFacturation($reference, $type_reglement, $informations_reglement, $date_reglement, $email,
                                  $societe, $nom, $prenom, $adresse, $code_postal, $ville, $id_pays, $id_forum,
                                  $autorisation, $transaction, $etat, $facturation)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'SET ';
        $requete .= '  type_reglement=        ' . $this->_bdd->echapper($type_reglement) . ',';
        $requete .= '  montant=               ' . '(SELECT IFNULL(SUM(montant), 0.0) FROM afup_inscription_forum WHERE reference = ' . $this->_bdd->echapper($reference) . '),';
        $requete .= '  date_reglement=        ' . $this->_bdd->echapper($date_reglement) . ',';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= '  societe=               ' . $this->_bdd->echapper($societe) . ',';
        $requete .= '  nom=                   ' . $this->_bdd->echapper($nom) . ',';
        $requete .= '  prenom=                ' . $this->_bdd->echapper($prenom) . ',';
        $requete .= '  email=                 ' . $this->_bdd->echapper($email) . ',';
        $requete .= '  adresse=               ' . $this->_bdd->echapper($adresse) . ',';
        $requete .= '  code_postal=           ' . $this->_bdd->echapper($code_postal) . ',';
        $requete .= '  ville=                 ' . $this->_bdd->echapper($ville) . ',';
        $requete .= '  id_pays=               ' . $this->_bdd->echapper($id_pays) . ',';
        $requete .= '  autorisation=          ' . $this->_bdd->echapper($autorisation) . ',';
        $requete .= '  transaction=           ' . $this->_bdd->echapper($transaction) . ',';
        $requete .= '  etat=                  ' . $etat . ',';
        $requete .= '  facturation=           ' . $this->_bdd->echapper($facturation) . ',';
        $requete .= '  id_forum=              ' . $id_forum . ' ';
        $requete .= 'WHERE';
        $requete .= '  reference=' . $this->_bdd->echapper($reference);

        return $this->_bdd->executer($requete);
    }

    function estFacture($reference)
    {
        $facture = $this->obtenir($reference, 'etat, facturation');
        if ($facture['facturation'] == AFUP_FORUM_FACTURE_A_ENVOYER) {
            $requete = 'UPDATE afup_inscription_forum ';
            $requete .= 'SET facturation=' . AFUP_FORUM_FACTURE_ENVOYEE . ' ';
            $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
            $this->_bdd->executer($requete);

            $requete = 'UPDATE afup_facturation_forum ';
            $requete .= 'SET facturation=' . AFUP_FORUM_FACTURE_ENVOYEE . ', date_facture= ' . time() . ' ';
            $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
            return $this->_bdd->executer($requete);
        }
        return true;
    }


    function supprimerFacturation($reference)
    {
        if ($this->_getNbInscriptions($reference) == 0) {
            $requete = 'DELETE FROM afup_facturation_forum WHERE reference=' . $this->_bdd->echapper($reference);
            return $this->_bdd->executer($requete);
        } else {
            return $this->_actualiserFacturation($reference);
        }
    }

    function _actualiserFacturation($reference)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'SET ';
        $requete .= '  montant= (SELECT IFNULL(SUM(montant), 0.0) FROM afup_inscription_forum WHERE reference = ' . $this->_bdd->echapper($reference) . ') ';
        $requete .= 'WHERE';
        $requete .= '  reference=' . $this->_bdd->echapper($reference);

        return $this->_bdd->executer($requete);
    }

    function enregistrerInformationsTransaction($reference, $autorisation, $transaction)
    {
        $time = time();
        $requete = <<<SQL
UPDATE afup_facturation_forum
SET
    autorisation = {$this->_bdd->echapper($autorisation)},
    transaction = {$this->_bdd->echapper($transaction)},
    date_reglement = {$time}
WHERE reference = {$this->_bdd->echapper($reference)}
SQL;
        return $this->_bdd->executer($requete);
    }

    function genererDevis($reference, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_facturation_forum WHERE reference=' . $this->_bdd->echapper($reference);
        $facture = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_inscription_forum WHERE reference=' . $this->_bdd->echapper($reference);
        $inscriptions = $this->_bdd->obtenirTous($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'Afup/Pays.php';
        $pays = new Pays($this->_bdd);

        // Construction du PDF

        $pdf = new PDF_Facture($configuration);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($facture['date_facture']) && !empty($facture['date_facture']) ? $facture['date_facture'] : time())));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . " " . $facture['prenom'];
        }

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, utf8_decode('Objet : Devis n°' . $reference));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, utf8_decode($facture['societe']) . "\n" . utf8_decode($facture['adresse']) . "\n" . utf8_decode($facture['code_postal']) . "\n" . utf8_decode($facture['ville']) . "\n" . utf8_decode($pays->obtenirNom($facture['id_pays'])));

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, utf8_decode("Devis concernant votre participation au forum organisé par l'Association Française des Utilisateurs de PHP (AFUP)."));
        // Cadre
        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100, 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

        $total = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            switch ($inscription['type_inscription']) {
                case AFUP_FORUM_PREMIERE_JOURNEE :
                    $code = 'FONC';
                    break;
                case AFUP_FORUM_DEUXIEME_JOURNEE :
                    $code = 'TECH';
                    break;
                case AFUP_FORUM_2_JOURNEES :
                    $code = '2JOU';
                    break;
                case AFUP_FORUM_2_JOURNEES_AFUP :
                    $code = 'AFUP';
                    break;
                case AFUP_FORUM_2_JOURNEES_ETUDIANT :
                    $code = 'ETUD';
                    break;
                case AFUP_FORUM_2_JOURNEES_PREVENTE :
                    $code = 'PREV';
                    break;
                case AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE :
                    $code = 'AFUP-PRE';
                    break;
                case AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE :
                    $code = 'ETUD-PRE';
                    break;
                case AFUP_FORUM_2_JOURNEES_COUPON :
                    $code = 'COUPON';
                    break;
            }

            $pdf->Cell(50, 5, $code, 1);
            $pdf->Cell(100, 5, utf8_decode($inscription['prenom']) . ' ' . utf8_decode($inscription['nom']), 1);
            $pdf->Cell(40, 5, utf8_decode($inscription['montant']) . utf8_decode(' '), 1);
            $total += $inscription['montant'];
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(150, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, $total . utf8_decode(' '), 1, 0, 'L', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');

        if (is_null($chemin)) {
            $pdf->Output('Devis - ' . ($facture['societe'] ? $facture['societe'] : $facture['nom'] . '_' . $facture['prenom']) . ' - ' . date('Y-m-d_H-i', $facture['date_facture']) . '.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }
    }

    /**
     * Génère une facture au format PDF
     *
     * @param string $reference Reference de la facture
     * @param string $chemin Chemin du fichier PDF à générer. Si ce chemin est omi, le PDF est renvoyé au navigateur.
     * @access public
     * @return bool
     */
    function genererFacture($reference, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_facturation_forum WHERE reference=' . $this->_bdd->echapper($reference);
        $facture = $this->_bdd->obtenirEnregistrement($requete);

        $requete = 'SELECT * FROM afup_inscription_forum WHERE reference=' . $this->_bdd->echapper($reference);
        $inscriptions = $this->_bdd->obtenirTous($requete);

        require_once 'Afup/Utils/Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'Afup/Pays.php';
        $pays = new Pays($this->_bdd);

        // Construction du PDF

        $pdf = new PDF_Facture($configuration);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($facture['date_facture']) && !empty($facture['date_facture']) ? $facture['date_facture'] : time())));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        if (empty($facture['societe'])) {
            $facture['societe'] = $facture['nom'] . " " . $facture['prenom'];
        }

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, utf8_decode('Objet : Facture n°' . $reference));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, utf8_decode($facture['societe']) . "\n" . utf8_decode($facture['adresse']) . "\n" . utf8_decode($facture['code_postal']) . "\n" . utf8_decode($facture['ville']) . "\n" . utf8_decode($pays->obtenirNom($facture['id_pays'])));

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, utf8_decode("Facture concernant votre participation au forum organisé par l'Association Française des Utilisateurs de PHP (AFUP)."));

        if ($facture['informations_reglement']) {
            $pdf->Ln(10);
            $pdf->Cell(32, 5, utf8_decode('Référence client : '));
            $infos = explode("\n", $facture['informations_reglement']);
            foreach ($infos as $info) {
                $pdf->Cell(100, 5, utf8_decode($info));
                $pdf->Ln();
                $pdf->Cell(32, 5);
            }
        }

        // Cadre
        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(100, 5, 'Personne inscrite', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

        $total = 0;
        foreach ($inscriptions as $inscription) {
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            switch ($inscription['type_inscription']) {
                case AFUP_FORUM_PREMIERE_JOURNEE :
                    $code = 'FONC';
                    break;
                case AFUP_FORUM_DEUXIEME_JOURNEE :
                    $code = 'TECH';
                    break;
                case AFUP_FORUM_2_JOURNEES :
                    $code = '2JOU';
                    break;
                case AFUP_FORUM_2_JOURNEES_AFUP :
                    $code = 'AFUP';
                    break;
                case AFUP_FORUM_2_JOURNEES_ETUDIANT :
                    $code = 'ETUD';
                    break;
                case AFUP_FORUM_2_JOURNEES_PREVENTE :
                    $code = 'PREV';
                    break;
                case AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE :
                    $code = 'AFUP-PRE';
                    break;
                case AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE :
                    $code = 'ETUD-PRE';
                    break;
                case AFUP_FORUM_2_JOURNEES_COUPON :
                    $code = 'COUPON';
                    break;
                case AFUP_FORUM_ORGANISATION :
                    $code = 'ORGANISATION';
                    break;
                case AFUP_FORUM_SPONSOR :
                    $code = 'SPONSOR';
                    break;
                case AFUP_FORUM_PRESSE :
                    $code = 'PRESSE';
                    break;
                case AFUP_FORUM_CONFERENCIER :
                    $code = 'CONFERENCIER';
                    break;
                case AFUP_FORUM_INVITATION :
                    $code = 'INVITATION';
                    break;
                case AFUP_FORUM_PROJET :
                    $code = 'PROJET';
                    break;
                case AFUP_FORUM_2_JOURNEES_SPONSOR :
                    $code = '2_JOURNEES_SPONSOR';
                    break;
                case AFUP_FORUM_PROF :
                    $code = 'PROF';
                    break;
                case AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE :
                    $code = 'PREMIERE_JOURNEE_ETUDIANT_PREVENTE';
                    break;
                case AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE :
                    $code = 'DEUXIEME_JOURNEE_ETUDIANT_PREVENTE';
                    break;
                case AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION :
                    $code = '2_JOURNEES_PREVENTE_ADHESION';
                    break;
                case AFUP_FORUM_PREMIERE_JOURNEE_AFUP :
                    $code = 'PREMIERE_JOURNEE_AFUP';
                    break;
                case AFUP_FORUM_DEUXIEME_JOURNEE_AFUP :
                    $code = 'DEUXIEME_JOURNEE_AFUP';
                    break;
                case AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT :
                    $code = 'PREMIERE_JOURNEE_ETUDIANT';
                    break;
                case AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT :
                    $code = 'DEUXIEME_JOURNEE_ETUDIANT';
                    break;
                default:
                    $code = 'XXX';
            }

            $pdf->Cell(50, 5, $code, 1);
            $pdf->Cell(100, 5, utf8_decode($inscription['prenom']) . ' ' . utf8_decode($inscription['nom']), 1);
            $pdf->Cell(40, 5, utf8_decode($inscription['montant']) . utf8_decode(' '), 1);
            $total += $inscription['montant'];
        }

        if ($facture['type_reglement'] == 1) { // Paiement par chèque
            $pdf->Ln();
            $pdf->Cell(50, 5, 'FRAIS', 1);
            $pdf->Cell(100, 5, utf8_decode('Paiement par chèque'), 1);
            $pdf->Cell(40, 5, '25' . utf8_decode(' '), 1);
            $total += 25;
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(150, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(40, 5, $total . utf8_decode(' '), 1, 0, 'L', 1);

        $pdf->Ln(15);
        if ($facture['etat'] == 4) {
            switch ($facture['type_reglement']) {
                case 0:
                    $type = 'par CB';
                    break;
                case 1:
                    $type = 'par chèque';
                    break;
                case 2:
                    $type = 'par virement';
                    break;
            }
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(130, 5);
            $pdf->Cell(60, 5, utf8_decode('Payé ' . $type . ' le ' . date('d/m/Y', $facture['date_reglement'])));
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Ln();
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');

        if (is_null($chemin)) {
            $pdf->Output('Facture - ' . ($facture['societe'] ? $facture['societe'] : $facture['nom'] . '_' . $facture['prenom']) . ' - ' . date('Y-m-d_H-i', $facture['date_facture']) . '.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }

        return $reference;
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param string|array $reference Invoicing reference as string, or the invoice itself
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerFacture($reference, $copyTresorier = true, $facturer = true)
    {
        require_once 'Afup/Mail.php';
        require_once 'Afup/Utils/Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        if (is_array($reference)) {
            $personne = $reference;
            $reference = $personne['reference'];
        } else {
            $personne = $this->obtenir($reference, 'email, nom, prenom');
        }

        $mail = new Mail();
        $receiver = array(
            'email' => $personne['email'],
            'name' => sprintf('%s %s', $personne['prenom'], $personne['nom'])
        );
        $parameters = array();

        if (!$copyTresorier) {
            // Bypass copy tresorier@afup.org
            $parameters['bcc_address'] = null;
        }

        $parameters['subject'] = "Facture événement AFUP";

        $data = array(
            'raison_sociale' => $configuration->obtenir('afup|raison_sociale'),
            'adresse' => $configuration->obtenir('afup|adresse'),
            'ville' => $configuration->obtenir('afup|code_postal') . " " . $configuration->obtenir('afup|ville'),
        );

        $chemin_facture = AFUP_CHEMIN_RACINE . 'cache' . DIRECTORY_SEPARATOR . 'fact' . $reference . '.pdf';
        $numeroFacture = $this->genererFacture($reference, $chemin_facture);

        $parameters += array(
            "attachments" => array(
                array(
                    "type" => "application/pdf",
                    "name" => 'facture-' . $numeroFacture . '.pdf',
                    "content" => base64_encode(file_get_contents($chemin_facture)),
                )
            )
        );

        @unlink($chemin_facture);

        $ok = $mail->send('facture-forum', $receiver, $data, $parameters);

        if ($ok && $facturer) {
            $this->estFacture($reference);
        }

        return $ok;
    }

    function envoyerATous($id_forum)
    {
        $requete = 'SELECT';
        $requete .= '  reference ';
        $requete .= 'FROM';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'WHERE etat = ' . AFUP_FORUM_ETAT_REGLE . ' ';
        $requete .= '  AND id_forum =' . $id_forum . ' ';
        $factures = $this->_bdd->obtenirTous($requete);

        $ok = true;
        foreach ($factures as $facture) {
            flush();
            if ($this->envoyerFacture($facture['reference'], false)) {
                Logs::log('Envoi par email de la facture n°' . $facture['reference'] . ' OK');
            } else {
                $ok = false;
                Logs::log('Envoi par email de la facture n°' . $facture['reference'] . ' Erreur');
            }
        }

        return $ok;
    }

    /**
     * Changement de la date de réglement d'une facture
     * @param integer $reference
     * @param integer $date_reglement
     */
    public function changerDateReglement($reference, $date_reglement)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_facturation_forum ';
        $requete .= 'SET ';
        $requete .= '  date_reglement = ' . intval($date_reglement) . ' ';
        $requete .= 'WHERE';
        $requete .= '  reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->executer($requete);
    }
}
