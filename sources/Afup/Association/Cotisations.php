<?php

namespace Afup\Site\Association;
use Afup\Site\Droits;
use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Configuration;
use Afup\Site\Utils\Mailing;
use Afup\Site\Utils\PDF_Facture;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Assert\Assertion;
use DateInterval;
use DateTime;

define('AFUP_COTISATIONS_REGLEMENT_ESPECES', 0);
define('AFUP_COTISATIONS_REGLEMENT_CHEQUE', 1);
define('AFUP_COTISATIONS_REGLEMENT_VIREMENT', 2);
define('AFUP_COTISATIONS_REGLEMENT_AUTRE', 3);
define('AFUP_COTISATIONS_REGLEMENT_ENLIGNE', 4);

define('AFUP_COTISATIONS_PAIEMENT_ERREUR', 0);
define('AFUP_COTISATIONS_PAIEMENT_REGLE', 1);
define('AFUP_COTISATIONS_PAIEMENT_ANNULE', 2);
define('AFUP_COTISATIONS_PAIEMENT_REFUSE', 3);

/**
 * Classe de gestion des personnes morales
 */
class Cotisations
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var Base_De_Donnees
     */
    var $_bdd;

    /**
     * @var Droits|null
     */
    private $_droits;

    /**
     * Constructeur.
     *
     * @param object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function __construct(&$bdd, $droits = null)
    {
        $this->_bdd = $bdd;
        $this->_droits = $droits;
    }

    /**
     * Renvoit la liste des cotisations concernant une personne
     *
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param string $champs Champs à renvoyer
     * @param string $ordre Tri des enregistrements
     * @param bool $associatif Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($type_personne, $id_personne, $champs = '*', $ordre = 'date_fin DESC', $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne;
        $requete .= '  AND id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    /**
     * Renvoit la cotisation demandée
     *
     * @param int $id Identifiant de la cotisation
     * @param string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoit le numéro de la prochaine facture au format : {année}-{index depuis le début de l'année}
     *
     * @access private
     * @return string
     */
    function _genererNumeroFacture()
    {
        $requete = 'SELECT';
        $requete .= "  MAX(CAST(SUBSTRING_INDEX(numero_facture, '-', -1) AS UNSIGNED)) + 1 ";
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  LEFT(numero_facture, 4)=' . $this->_bdd->echapper(date('Y'));
        $requete .= '  OR LEFT(numero_facture, 10)=' . $this->_bdd->echapper("COTIS-" . date('Y'));
        $index = $this->_bdd->obtenirUn($requete);
        return 'COTIS-' . date('Y') . '-' . (is_null($index) ? 1 : $index);
    }

    /**
     * Ajoute une cotisation
     *
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param float $montant Adresse de la personne
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @param int $date_debut Date de début de la
     * cotisation
     * @param int $date_fin Date de fin de la cotisation
     * @param string $commentaires Commentaires concernnant la cotisation
     * @access public
     * @return bool     Succès de l'ajout
     */
    function ajouter($type_personne, $id_personne, $montant, $type_reglement,
                     $informations_reglement, $date_debut, $date_fin, $commentaires)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_cotisations (type_personne, id_personne, montant, type_reglement , informations_reglement,';
        $requete .= '                    date_debut, date_fin, numero_facture, token, commentaires) ';
        $requete .= 'VALUES (';
        $requete .= $type_personne . ',';
        $requete .= $id_personne . ',';
        $requete .= $montant . ',';
        $requete .= ($type_reglement === null ? 'NULL' : $type_reglement) . ',';
        $requete .= $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= $date_debut . ',';
        $requete .= $date_fin . ',';
        $requete .= $this->_bdd->echapper($this->_genererNumeroFacture()) . ',';
        $requete .= $this->_bdd->echapper(base64_encode(random_bytes(30))) . ',';
        $requete .= $this->_bdd->echapper($commentaires) . ')';

        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return true;
    }

    /**
     * Modifie une cotisation
     *
     * @param int $id Identifiant de la cotisation à modifier
     * @param int $type_personne Type de la personne (morale ou physique)
     * @param int $id_personne Identifiant de la personne
     * @param float $montant Adresse de la personne
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @param int $date_debut Date de début de la
     * cotisation
     * @param int $date_fin Date de fin de la cotisation
     * @param string $commentaires Commentaires concernnant la cotisation
     * @access public
     * @return bool Succès de la modification
     */
    function modifier($id, $type_personne, $id_personne, $montant, $type_reglement,
                      $informations_reglement, $date_debut, $date_fin, $commentaires)
    {
        $requete = 'UPDATE';
        $requete .= '  afup_cotisations ';
        $requete .= 'SET';
        $requete .= '  type_personne=' . $type_personne . ',';
        $requete .= '  id_personne=' . $id_personne . ',';
        $requete .= '  montant=' . $montant . ',';
        $requete .= '  type_reglement=' . $type_reglement . ',';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($informations_reglement) . ',';
        $requete .= '  date_debut=' . $date_debut . ',';
        $requete .= '  date_fin=' . $date_fin . ',';
        $requete .= '  commentaires=' . $this->_bdd->echapper($commentaires) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;
        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return true;
    }

    function estDejaReglee($cmd)
    {
        $requete = 'SELECT';
        $requete .= '  1 ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($cmd);
        return $this->_bdd->obtenirUn($requete);
    }

    function notifierRegelementEnLigneAuTresorier($cmd, $total, $autorisation, $transaction, UserRepository $userRepository)
    {
        /**
         * @var $configuration Configuration
         */
        $configuration = $GLOBALS['AFUP_CONF'];

        list($ref, $date, $type_personne, $id_personne, $reste) = explode('-', $cmd, 5);

        if (AFUP_PERSONNES_MORALES == $type_personne) {
            $personnes = new Personnes_Morales($this->_bdd);
            $infos = $personnes->obtenir($id_personne, 'nom, prenom, email');
        } else {
            $user = $userRepository->get($id_personne);
            Assertion::notNull($user);
            $infos = [
                'nom' => $user->getLastName(),
                'prenom' => $user->getFirstName(),
                'email' => $user->getEmail(),
            ];
        }

        $sujet = "Paiement cotisation AFUP\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Une cotisation annuelle AFUP a été réglée.\n\n";
        $corps .= "Personne : " . $infos['nom'] . " " . $infos['prenom'] . " (" . $infos['email'] . ")\n";
        $corps .= "URL : " . $configuration->obtenir('web|path') . "pages/administration/index.php?page=cotisations&type_personne=" . $type_personne . "&id_personne=" . $id_personne . "\n";
        $corps .= "Commande : " . $cmd . "\n";
        $corps .= "Total : " . $total . "\n";
        $corps .= "Autorisation : " . $autorisation . "\n";
        $corps .= "Transaction : " . $transaction . "\n\n";

        $expediteur = $GLOBALS['conf']->obtenir('mails|email_expediteur');
        $ok = Mailing::envoyerMail(new Message($sujet, new MailUser($expediteur), MailUserFactory::tresorier()), $corps);

        if (false === $ok) {
            return false;
        }
    }

    function validerReglementEnLigne($cmd, $total, $autorisation, $transaction)
    {
        $reference = substr($cmd, 0, strlen($cmd) - 4);
        $verif = substr($cmd, strlen($cmd) - 3, strlen($cmd));

        if (substr($cmd, 0, 1) === 'F') {
            // This is an invoice ==> we dont have to create a new cotisation, just update the existing one
            $invoiceNumber = substr($cmd, 1);
            $cotisation = $this->getByInvoice($invoiceNumber);

            $this
                ->updatePayment(
                    $cotisation['id'],
                    AFUP_COTISATIONS_REGLEMENT_ENLIGNE, "autorisation : " . $autorisation . " / transaction : " . $transaction
                );

        } elseif (substr(md5($reference), -3) == strtolower($verif) and !$this->estDejaReglee($cmd)) {
            list($ref, $date, $type_personne, $id_personne, $reste) = explode('-', $cmd, 5);
            $date_debut = mktime(0, 0, 0, substr($date, 2, 2), substr($date, 0, 2), substr($date, 4, 4));

            $cotisation = $this->obtenirDerniere($type_personne, $id_personne);
            $date_fin_precedente = $cotisation['date_fin'];

            if ($date_fin_precedente > 0) {
                $date_debut = strtotime('+1day', $date_fin_precedente);
            }

            $date_fin = $this->finProchaineCotisation($cotisation)->format('U');
            $result = $this->ajouter($type_personne,
                $id_personne,
                $total,
                AFUP_COTISATIONS_REGLEMENT_ENLIGNE,
                $cmd,
                $date_debut,
                $date_fin,
                "autorisation : " . $autorisation . " / transaction : " . $transaction);
        } else {
            $result = false;
        }

        return $result;
    }

    public function getAccountFromCmd($cmd)
    {
        list($ref, $date, $memberType, $memberId, $stuff) = explode('-', $cmd, 5);

        return ['type' => $memberType, 'id' => $memberId];
    }

    /**
     * Supprime une cotisation
     *
     * @param int $id Identifiant de la cotisation à supprimer
     * @access public
     * @return bool Succès de la suppression
     */
    function supprimer($id)
    {
        $requete = 'DELETE FROM afup_cotisations WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    /**
     * Génère une facture au format PDF
     *
     * @param int $id_cotisation Identifiant de la cotisation
     * @param string $chemin Chemin du fichier PDF à générer. Si ce chemin est omi, le PDF est renvoyé au navigateur.
     * @access public
     * @return int Le numero de la facture
     */
    function genererFacture($id_cotisation, $chemin = null)
    {
        $requete = 'SELECT * FROM afup_cotisations WHERE id=' . $id_cotisation;
        $cotisation = $this->_bdd->obtenirEnregistrement($requete);

        $table = $cotisation['type_personne'] == AFUP_PERSONNES_MORALES ? 'afup_personnes_morales' : 'afup_personnes_physiques';
        $requete = 'SELECT * FROM ' . $table . ' WHERE id=' . $cotisation['id_personne'];
        $personne = $this->_bdd->obtenirEnregistrement($requete);


        $configuration = $GLOBALS['AFUP_CONF'];

        // Construction du PDF

        $pdf = new PDF_Facture($configuration);
        $pdf->AddPage();

        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', $cotisation['date_debut']));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();

        // A l'attention du client [adresse]
        $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(130, 5, utf8_decode('Objet : Facture n°' . $cotisation['numero_facture']));
        $pdf->SetFont('Arial', '', 10);

        if ($cotisation['type_personne'] == AFUP_PERSONNES_MORALES) {
            $nom = $personne['raison_sociale'];
        } else {
            $nom = $personne['prenom'] . ' ' . $personne['nom'];
        }
        $pdf->Ln(10);
        $pdf->MultiCell(130, 5, utf8_decode($nom . "\n" . $personne['adresse'] . "\n" . $personne['code_postal'] . "\n" . $personne['ville']));

        $pdf->Ln(15);

        $pdf->MultiCell(180, 5, utf8_decode("Facture concernant votre adhésion à l'Association Française des Utilisateurs de PHP (AFUP)."));
        // Cadre
        $pdf->Ln(10);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(50, 5, 'Code', 1, 0, 'L', 1);
        $pdf->Cell(100, 5, utf8_decode('Désignation'), 1, 0, 'L', 1);
        $pdf->Cell(40, 5, 'Prix', 1, 0, 'L', 1);

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(50, 5, 'ADH', 1);
        $pdf->Cell(100, 5, utf8_decode("Adhésion AFUP jusqu'au " . date('d/m/Y', $cotisation['date_fin'])), 1);
        $pdf->Cell(40, 5, utf8_decode($cotisation['montant'] . ' '), 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        $pdf->Ln(15);
        $pdf->Cell(10, 5, utf8_decode('Lors de votre règlement, merci de préciser la mention : "Facture n°' . $cotisation['numero_facture']) . '"');

        if (is_null($chemin)) {
            $pdf->Output('facture-' . $cotisation['numero_facture'] . '.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }

        return $cotisation['numero_facture'];
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param   int $id_cotisation Identifiant de la cotisation
     * @access public
     * @return bool Succès de l'envoi
     */
    public function envoyerFacture($id_cotisation, Mailer $mailer, UserRepository $userRepository)
    {
        $configuration = $GLOBALS['AFUP_CONF'];

        $personne = $this->obtenir($id_cotisation, 'type_personne, id_personne');

        if ($personne['type_personne'] == AFUP_PERSONNES_MORALES) {
            $personnePhysique = new Personnes_Morales($this->_bdd);
            $contactPhysique = $personnePhysique->obtenir($personne['id_personne'], 'nom, prenom, email');
        } else {
            $user = $userRepository->get($personne['id_personne']);
            Assertion::notNull($user);
            $contactPhysique = [
                'nom'=> $user->getLastName(),
                'prenom'=> $user->getFirstName(),
                'email'=> $user->getEmail(),
            ];
        }

        $corps = "Bonjour,<br />";
        $corps .= "<p>Veuillez trouver ci-joint la facture correspondant à votre adhésion à l'AFUP.</p>";
        $corps .= "<p>Nous restons à votre disposition pour toute demande complémentaire.</p>";
        $corps .= "<p>Le bureau</p>";
        $corps .= $configuration->obtenir('afup|raison_sociale') . "<br />";
        $corps .= $configuration->obtenir('afup|adresse') . "<br />";
        $corps .= $configuration->obtenir('afup|code_postal') . " " . $configuration->obtenir('afup|ville') . "<br />";

        $cheminFacture = AFUP_CHEMIN_RACINE . 'cache/fact' . $id_cotisation . '.pdf';
        $numeroFacture = $this->genererFacture($id_cotisation, $cheminFacture);

        $message = new Message('Facture AFUP', null, new MailUser(
            $contactPhysique['email'],
            sprintf('%s %s', $contactPhysique['prenom'], $contactPhysique['nom'])
        ));
        $message->addAttachment(new Attachment(
                $cheminFacture,
                'facture-'.$numeroFacture.'.pdf',
                'base64',
                'application/pdf'
            ));
        $ok = $mailer->sendTransactional($message, $corps);
        @unlink($cheminFacture);

        return $ok;
    }

    /**
     * Retourne la dernière cotisation d'une personne morale
     *
     * @param    int $id_personne Identifiant de la personne
     * @access    public
     * @return    array
     */
    function obtenirDerniere($type_personne, $id_personne)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne . ' ';
        $requete .= '  AND id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY';
        $requete .= '  date_fin DESC ';
        $requete .= 'LIMIT 0, 1 ';
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Retourne la date de début d'une cotisation.
     *
     * Cette date est déterminée par la date de fin de la cotisation précédente
     * s'il y en a une ou alors sur la date du jour dans le cas contraire.
     *
     * @param    int $type_personne Identifiant du type de personne
     * @param    int $id_personne Identifiant de la personne
     * @access    public
     * @return    int                    Timestamp de la date de la cotisation
     */
    function obtenirDateDebut($type_personne, $id_personne)
    {
        $requete = 'SELECT';
        $requete .= '  date_fin ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  type_personne=' . $type_personne . ' ';
        $requete .= 'AND';
        $requete .= ' id_personne=' . $id_personne . ' ';
        $requete .= 'ORDER BY';
        $requete .= ' date_fin DESC';
        $date_debut = $this->_bdd->obtenirUn($requete);

        if ($date_debut !== false) {
            return $date_debut;
        } else {
            return time();
        }
    }

    /**
     * @param array $cotisation from Afup_Personnes_Physiques::obtenirDerniereCotisation
     * @return DateTime: Date of end of next subscription
     */
    public function finProchaineCotisation($cotisation)
    {
        if ($cotisation === false) {
            $endSubscription = new DateTime();
        } else {
            $endSubscription = DateTime::createFromFormat('U', $cotisation['date_fin']);
        }
        $base = $now = new DateTime();

        $year = new DateInterval('P1Y');

        if ($endSubscription > $now) {
            $base = $endSubscription;
        }

        $base->add($year);
        return $base;
    }

    /**
     * Renvoit la cotisation demandée
     *
     * @param string $invoiceId Identifiant de la facture
     * @param string|null $token Token de la facture. Si null, pas de vérification
     * @return array
     */
    public function getByInvoice($invoiceId, $token = null)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ';
        $requete .= 'WHERE';
        $requete .= '  numero_facture = ' . $this->_bdd->echapper($invoiceId);
        if ($token !== null) {
            $requete .= ' AND token = ' . $this->_bdd->echapper($token);
        }

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Modifie une cotisation
     *
     * @param int $id Identifiant de la cotisation à modifier
     * @param int $type_reglement Type de règlement (espèces, chèque, virement)
     * @param string $informations_reglement Informations concernant le règlement (numéro de chèque, de virement etc.)
     * @return bool Succès de la modification
     */
    function updatePayment($id, $type_reglement, $informations_reglement)
    {
        $requete = 'UPDATE';
        $requete .= '  afup_cotisations ';
        $requete .= 'SET';
        $requete .= '  type_reglement=' . $type_reglement . ',';
        $requete .= '  informations_reglement=' . $this->_bdd->echapper($informations_reglement);
        $requete .= ' WHERE';
        $requete .= '  id=' . $id;
        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return true;
    }

    public function isCurrentUserAllowedToReadInvoice ($invoiceId)
    {
        if (!$this->_droits) {
            throw new \RuntimeException('La variable $_droits ne doit pas être null.');
        }

        $sql = 'SELECT type_personne, id_personne FROM afup_cotisations WHERE id = ' . $this->_bdd->echapper($invoiceId);
        $result = $this->_bdd->obtenirEnregistrement($sql);

        if (!$result) {
            return false;
        }

        /**
         * si type_personne = 0, alors personne physique: id_personne doit être identique l'id de l'utilisateur connecté
         */
        if ($result['type_personne'] === "0") {
            return $result['id_personne'] == $this->_droits->obtenirIdentifiant();
        }

        /**
         * si type_personne = 1, alors personne morale: id_personne doit être égale à compagnyId de l'utilisateur connecté
         * qui doit aussi avoir le droit "ROLE_COMPAGNY_MANAGER"
         */
        if ($result['type_personne'] == AFUP_PERSONNES_MORALES) {
            return $this->_droits->verifierDroitManagerPersonneMorale($result['id_personne']);
        }

        return false;
    }
}
