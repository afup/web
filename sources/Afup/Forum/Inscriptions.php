<?php
namespace Afup\Site\Forum;

use Afup\Site\Utils\Mail;

class Inscriptions
{
    /**
     * Instance de la couche d'abstraction ï¿½ la base de donnï¿½es
     * @var     object
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction ï¿½ la base de donnï¿½es
     * @access public
     * @return void
     */
    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une inscription
     *$inscrits =
     * @param  int $id Identifiant de la personne
     * @param  string $champs Champs ï¿½ renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = 'i.*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';

        $requete .= 'WHERE i.id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoie la liste des inscriptions pour lesquels md5(concat('id', 'reference')) = $code_md5 (1er argument)
     * (concaténation des champs 'id' et 'reference', passée à la fonction md5)
     *
     * @param $code_md5 string Md5 de la concaténation des champs "id" et "reference"
     * @param string $champs Liste des champs à récupérer en BD
     * @return array
     */
    function obtenirInscription($code_md5, $champs = 'i.*')
    {
        $requete = "SELECT $champs FROM afup_inscription_forum i ";
        $requete .= "LEFT JOIN afup_facturation_forum f ON i.reference = f.reference ";
        $requete .= "WHERE md5(CONCAT(i.id, i.reference)) = '$code_md5'";

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Retrieve the registrations associated to the same reference
     * <p>Used by example to send a confirmation email to every people associated
     * to the same payment.</p>
     * @param string $reference The reference shared
     * @return array The people we want ;)
     */
    public function getRegistrationsByReference($reference)
    {
        $ref = $this->_bdd->echapper($reference);
        $sql = <<<SQL
SELECT *
FROM afup_inscription_forum
WHERE reference = $ref;
SQL;
        $registrations = $this->_bdd->obtenirTous($sql);
        return $registrations;
    }

    /**
     * Send the "convocation" email to every people attending to the specified event.
     * @param int $id_forum Forum's ID
     * @param string $template Mandrill template's identifier
     * @return bool Always TRUE (due to legacy code)
     */
    public function envoyerEmailConvocation($id_forum, $template)
    {
        require_once dirname(__FILE__) . '/../Utils/Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        // Get all visitors with "good" state (good to receive the email)
        // No speakers.
        $requete = <<<SQL
SELECT
  i.*, f.societe, md5(CONCAT(i.id, i.reference)) as md5key, af.path
FROM
  afup_inscription_forum i
LEFT JOIN
  afup_facturation_forum f ON i.reference = f.reference
INNER JOIN
  afup_forum af ON i.id_forum = af.id
WHERE  i.id_forum = $id_forum
AND i.type_inscription <> 12
AND i.etat IN (0, 4, 5, 6, 7, 8)
ORDER BY i.date
;
SQL;
        $inscrits = $this->_bdd->obtenirTous($requete);


        $mailer = new Mail();

        $listSent = array();

        // Send to each visitor
        $total = count($inscrits);
        foreach ($inscrits as $nb => $inscrit) {
            $sent = $mailer->send(
                $template,
                array(
                    'name' => $inscrit['prenom'] . " " . $inscrit['nom'],
                    'email' => $inscrit['email'],
                ),
                $inscrit,
                array(
                    'bcc_address' => false // avoid blind copy… spam inside! ;)
                ),
                true
            );
            if ($sent) {
                $listSent[] = "{$inscrit['prenom']} {$inscrit['nom']} : {$inscrit['email']}";
            }
        }

        // Send confirmation
        $count = count($listSent);
        $msg = "<p>Voici la liste des convocations envoyées ($count/$total, template $template) :</p>";
        $msg .= "<ol>";
        $msg .= "<li>" . implode("</li>\n<li>", $listSent) . "</li>";
        $msg .= "</ol>";
        $mailer->sendSimpleMessage("Liste des convocations envoyées", $msg);

        return true;
    }

    function obtenirSuivi($id_forum)
    {
        $forum = new Forum($this->_bdd);
        $id_forum_precedent = $forum->obtenirPrecedent($id_forum);

        $requete = 'SELECT ';
        $requete .= '  COUNT(*) as nombre, ';
        $requete .= '  UNIX_TIMESTAMP(FROM_UNIXTIME(date, \'%Y-%m-%d\')) as jour, ';
        $requete .= '  id_forum ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'WHERE ';
        $requete .= '  i.id_forum IN (' . (int)$id_forum . ', ' . (int)$id_forum_precedent . ') ';
        $requete .= 'AND ';
        $requete .= '  etat <> 1 ';
        $requete .= 'GROUP BY jour, id_forum ';
        $requete .= 'ORDER BY jour DESC ';
        $nombre_par_date = $this->_bdd->obtenirTous($requete);

        foreach ($nombre_par_date as $nombre) {
            $inscrits[$nombre['id_forum']][$nombre['jour']] = $nombre['nombre'];
        }

        if (!isset($inscrits[$id_forum])) {
            // Pas encore d'inscrits
            return false;
        }

        $debut = $forum->obtenirDebut($id_forum);
        $debut_precedent = $forum->obtenirDebut($id_forum_precedent);

        $premiere_inscription = min(array_keys($inscrits[$id_forum]));
        $premiere_inscription_precedent = min(array_keys($inscrits[$id_forum_precedent]));

        $debut_jd = gregoriantojd(date("m", $debut), date("d", $debut), date("Y", $debut));
        $premiere_inscription_jd = gregoriantojd(date("m", $premiere_inscription), date("d", $premiere_inscription), date("Y", $premiere_inscription));

        $debut_precedent_jd = gregoriantojd(date("m", $debut_precedent), date("d", $debut_precedent), date("Y", $debut_precedent));
        $premiere_inscription_precedent_jd = gregoriantojd(date("m", $premiere_inscription_precedent), date("d", $premiere_inscription_precedent), date("Y", $premiere_inscription_precedent));

        $ecart = max($debut_jd - $premiere_inscription_jd, $debut_precedent_jd - $premiere_inscription_precedent_jd);

        $suivis = array();
        $total_cumule = 0;
        $total_cumule_precedent = 0;
        $aujourdhui = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        for ($i = $ecart; $i--; $i == 0) {
            $jour = mktime(0, 0, 0, date("m", $debut), date("d", $debut) - $i, date("Y", $debut));
            if (isset($inscrits[$id_forum][$jour])) {
                $total_cumule += $inscrits[$id_forum][$jour];
            }

            $jour_precedent = mktime(0, 0, 0, date("m", $debut_precedent), date("d", $debut_precedent) - $i, date("Y", $debut_precedent));
            if (isset($inscrits[$id_forum_precedent][$jour_precedent])) {
                $total_cumule_precedent += $inscrits[$id_forum_precedent][$jour_precedent];
            }

            if ($jour < $aujourdhui) {
                $periode = "avant";
            } elseif ($jour > $aujourdhui) {
                $periode = "apres";
            } else {
                $periode = "aujourdhui";
            }

            $suivis[] = array(
                'periode' => $periode,
                'jour' => date("d/m/Y", $jour),
                'n' => $total_cumule,
                'n_1' => $total_cumule_precedent,
            );
        }

        return $suivis;
    }

    function obtenirListePourEmargement($id_forum = null)
    {
        $requete = 'SELECT';
        $requete .= '  i.*, f.societe ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        $requete .= 'AND    i.type_inscription NOT IN (9, 10, 11, 12, 15) '; // pas orga, conférencier, sponsor, presse
        $requete .= 'ORDER BY i.nom ASC';
        $liste_emargement = array();
        $liste = $this->_bdd->obtenirTous($requete);

        $derniere_lettre = "";
        foreach ($liste as $inscrit) {
            $premiere_lettre = strtoupper($inscrit['nom'][0]);
            if ($derniere_lettre != $premiere_lettre) {
                $liste_emargement[] = array(
                    'nom' => $premiere_lettre,
                    'etat' => -1,
                );
                $derniere_lettre = $premiere_lettre;
            }
            $liste_emargement[] = $inscrit;
        }

        return $liste_emargement;
    }

    function obtenirListePourEmargementConferencierOrga($id_forum = null)
    {
        $requete = 'SELECT';
        $requete .= '  i.*, f.societe ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        $requete .= 'AND    i.type_inscription IN (9, 10, 11, 12, 15) '; // seulement orga, conférencier, sponsor, presse
        $requete .= 'ORDER BY i.nom ASC';
        $liste_emargement = array();
        $liste = $this->_bdd->obtenirTous($requete);

        $derniere_lettre = "";
        foreach ($liste as $inscrit) {
            $premiere_lettre = strtoupper($inscrit['nom'][0]);
            if ($derniere_lettre != $premiere_lettre) {
                $liste_emargement[] = array(
                    'nom' => $premiere_lettre,
                    'etat' => -1,
                );
                $derniere_lettre = $premiere_lettre;
            }
            $liste_emargement[] = $inscrit;
        }

        return $liste_emargement;
    }

    function obtenirListePourBadges($id_forum = null, $id = null)
    {
        $requete = 'SELECT';
        $requete .= '  i.*, f.societe, f.type_reglement ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        if (isset($id) and $id > 0) {
            $requete .= 'AND  i.id =' . $id . ' ';
        }
        $requete .= 'ORDER BY i.date';
        return $this->_bdd->obtenirTous($requete);

    }

    /**
     * Renvoit la liste des inscriptions au forum
     *
     * @param  string $champs Champs ï¿½ renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($id_forum = null,
                          $champs = 'i.*',
                          $ordre = 'i.date',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE 1=1 ';
        $requete .= '  AND i.id_forum =' . $id_forum . ' ';
        if ($filtre) {
            $requete .= 'i.nom LIKE \'%' . $filtre . '%\' ';
            $requete .= 'OR f.societe LIKE \'%' . $filtre . '%\' ';
        }
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function ajouterInscription($id_forum, $reference, $type_inscription, $civilite, $nom, $prenom,
                                $email, $telephone, $coupon, $citer_societe, $newsletter_afup,
                                $newsletter_nexen, $commentaires = null, $mobilite_reduite = 0, $mail_partenaire = 0,
                                $etat = AFUP_FORUM_ETAT_CREE, $facturation = AFUP_FORUM_FACTURE_A_ENVOYER)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  afup_inscription_forum (id_forum, date, reference, type_inscription, montant,
                               civilite, nom, prenom, email, telephone, coupon, citer_societe,
                               newsletter_afup, newsletter_nexen, commentaires, etat, facturation, mobilite_reduite, mail_partenaire) ';
        $requete .= 'VALUES (';
        $requete .= $id_forum . ',';
        $requete .= time() . ',';
        $requete .= $this->_bdd->echapper($reference) . ',';
        $requete .= $this->_bdd->echapper($type_inscription) . ',';
        $requete .= $GLOBALS['AFUP_Tarifs_Forum'][$type_inscription] . ',';
        $requete .= $this->_bdd->echapper($civilite) . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= $this->_bdd->echapper($prenom) . ',';
        $requete .= $this->_bdd->echapper($email) . ',';
        $requete .= $this->_bdd->echapper($telephone) . ',';
        $requete .= $this->_bdd->echapper($coupon) . ',';
        $requete .= $this->_bdd->echapper($citer_societe) . ',';
        $requete .= $this->_bdd->echapper($newsletter_afup) . ',';
        $requete .= $this->_bdd->echapper($newsletter_nexen) . ',';
        $requete .= $this->_bdd->echapper($commentaires) . ',';
        $requete .= $etat . ',';
        $requete .= $this->_bdd->echapper($facturation) . ',';
        $requete .= $this->_bdd->echapper($mobilite_reduite) . ',';
        $requete .= $this->_bdd->echapper($mail_partenaire) . ')';

        return $this->_bdd->executer($requete);
    }

    function modifierInscription($id, $reference, $type_inscription, $civilite, $nom, $prenom,
                                 $email, $telephone, $coupon, $citer_societe, $newsletter_afup,
                                 $newsletter_nexen, $mail_partenaire, $commentaires, $etat, $facturation, $mobilite_reduite = 0)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_inscription_forum ';
        $requete .= 'SET';
        $requete .= '  reference=' . $this->_bdd->echapper($reference) . ',';
        $requete .= '  type_inscription=' . $this->_bdd->echapper($type_inscription) . ',';
        $requete .= '  montant=' . $GLOBALS['AFUP_Tarifs_Forum'][$type_inscription] . ',';
        $requete .= '  civilite=' . $this->_bdd->echapper($civilite) . ',';
        $requete .= '  nom=' . $this->_bdd->echapper($nom) . ',';
        $requete .= '  prenom=' . $this->_bdd->echapper($prenom) . ',';
        $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
        $requete .= '  telephone=' . $this->_bdd->echapper($telephone) . ',';
        $requete .= '  coupon=' . $this->_bdd->echapper($coupon) . ',';
        $requete .= '  citer_societe=' . $this->_bdd->echapper($citer_societe) . ',';
        $requete .= '  newsletter_afup=' . $this->_bdd->echapper($newsletter_afup) . ',';
        $requete .= '  newsletter_nexen=' . $this->_bdd->echapper($newsletter_nexen) . ',';
        $requete .= '  mail_partenaire=' . $this->_bdd->echapper($mail_partenaire) . ',';
        $requete .= '  commentaires=' . $this->_bdd->echapper($commentaires) . ',';
        $requete .= '  etat=' . $this->_bdd->echapper($etat) . ',';
        $requete .= '  facturation=' . $this->_bdd->echapper($facturation) . ',';
        $requete .= '  mobilite_reduite=' . $this->_bdd->echapper($mobilite_reduite) . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

        $this->modifierEtatInscription($reference, $etat);

        return $this->_bdd->executer($requete);
    }

    function supprimerInscription($id)
    {
        $requete = 'DELETE FROM afup_inscription_forum WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    function modifierEtatInscription($reference, $etat)
    {
        $requete = 'UPDATE afup_inscription_forum ';
        $requete .= 'SET etat=' . $etat . ' ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        $this->_bdd->executer($requete);

        $requete = 'UPDATE afup_facturation_forum ';
        $requete .= 'SET etat=' . $etat . ' ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->executer($requete);
    }

    function ajouterRappel($email, $id_forum = null)
    {
        if ($id_forum == null) {
            require_once dirname(__FILE__) . '/Forum.php';
            $forum = new Forum($this->_bdd);
            $id_forum = $forum->obtenirDernier();
        }
        $requete = 'INSERT INTO afup_inscriptions_rappels (email, date, id_forum) VALUES (' . $this->_bdd->echapper($email) . ', ' . time() . ', ' . $id_forum . ')';
        return $this->_bdd->executer($requete);
    }

    function obtenirNombreInscrits($id_forum = null)
    {
        $statistiques = $this->obtenirStatistiques($id_forum);
        $nombresInscrits = max($statistiques['premier_jour']['inscrits'], $statistiques['second_jour']['inscrits']);

        return $nombresInscrits;
    }

    function obtenirStatistiques($id_forum = null)
    {
        $statistiques = array();

        // Premier jour
        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['premier_jour']['inscrits'] = $this->_bdd->obtenirUn($requete);

        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_INVITE . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['premier_jour']['confirmes'] = $this->_bdd->obtenirUn($requete);

        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat = ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ' ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['premier_jour']['en_attente_de_reglement'] = $this->_bdd->obtenirUn($requete);

        // Second jour
        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['second_jour']['inscrits'] = $this->_bdd->obtenirUn($requete);

        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_INVITE . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['second_jour']['confirmes'] = $this->_bdd->obtenirUn($requete);

        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat = ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ' ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ','
            . AFUP_FORUM_2_JOURNEES . ','
            . AFUP_FORUM_2_JOURNEES_AFUP . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_COUPON . ','
            . AFUP_FORUM_2_JOURNEES_SPONSOR . ','
            . AFUP_FORUM_PROF . ','
            . AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE . ','
            . AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION . ')';
        $statistiques['second_jour']['en_attente_de_reglement'] = $this->_bdd->obtenirUn($requete);

        // Nombre de personnes validées par type d'inscription
        $requete = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ', ' . AFUP_FORUM_ETAT_INVITE . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['confirmes'] = $this->_bdd->obtenirAssociatif($requete);

        $requete = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['payants'] = $this->_bdd->obtenirAssociatif($requete);

        $requete = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= 'AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['inscrits'] = $this->_bdd->obtenirAssociatif($requete);

        $requete = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['payants'] = $this->_bdd->obtenirAssociatif($requete);

        $requete = 'SELECT concat(type_inscription,\'-\',etat) , COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        // $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY concat(type_inscription,\'-\',etat)';
        //$statistiques['types_inscriptions']['etat'] = $this->_bdd->obtenirAssociatif($requete);

        $requete = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        // $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY id_forum';
        //$statistiques['types_inscriptions']['total'] = $this->_bdd->obtenirUn($requete);

        return $statistiques;
    }

    public function obtenirListeEmailAncienVisiteurs()
    {
        $requete = "SELECT group_concat(DISTINCT email SEPARATOR ';')
                    FROM afup_inscription_forum
                    WHERE `email` <> ''
                    AND right(email, 9) <> '@afup.org'
                    AND type_inscription <> 12
                    AND locate('xxx', email) = 0";
        return $this->_bdd->obtenirUn($requete);
    }
}

?>
