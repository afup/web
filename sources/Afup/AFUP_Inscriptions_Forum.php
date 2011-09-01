<?php
define('EURO'                             , '€');

define('AFUP_FORUM_ETAT_CREE'             , 0);
define('AFUP_FORUM_ETAT_ANNULE'           , 1);
define('AFUP_FORUM_ETAT_ERREUR'           , 2);
define('AFUP_FORUM_ETAT_REFUSE'           , 3);
define('AFUP_FORUM_ETAT_REGLE'            , 4);
define('AFUP_FORUM_ETAT_INVITE'           , 5);
define('AFUP_FORUM_ETAT_ATTENTE_REGLEMENT', 6);
define('AFUP_FORUM_ETAT_CONFIRME'         , 7);
define('AFUP_FORUM_ETAT_A_POSTERIORI'     , 8);

define('AFUP_FORUM_FACTURE_A_ENVOYER', 0);
define('AFUP_FORUM_FACTURE_ENVOYEE'  , 1);
define('AFUP_FORUM_FACTURE_RECUE'    , 2);

define('AFUP_FORUM_PREMIERE_JOURNEE'            , 0);
define('AFUP_FORUM_DEUXIEME_JOURNEE'            , 1);
define('AFUP_FORUM_2_JOURNEES'                  , 2);
define('AFUP_FORUM_2_JOURNEES_AFUP'             , 3);
define('AFUP_FORUM_2_JOURNEES_ETUDIANT'         , 4);
define('AFUP_FORUM_2_JOURNEES_PREVENTE'         , 5);
define('AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE'    , 6);
define('AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE', 7);
define('AFUP_FORUM_2_JOURNEES_COUPON'           , 8);
define('AFUP_FORUM_ORGANISATION'                , 9);
define('AFUP_FORUM_SPONSOR'                     , 10);
define('AFUP_FORUM_PRESSE'                      , 11);
define('AFUP_FORUM_CONFERENCIER'                , 12);
define('AFUP_FORUM_INVITATION'                  , 13);
define('AFUP_FORUM_PROJET'                      , 14);
define('AFUP_FORUM_2_JOURNEES_SPONSOR'          , 15);
define('AFUP_FORUM_PROF'                        , 16);

define('AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE', 0);
define('AFUP_FORUM_REGLEMENT_CHEQUE'        , 1);
define('AFUP_FORUM_REGLEMENT_VIREMENT'      , 2);
define('AFUP_FORUM_REGLEMENT_AUCUN'         , 3);
define('AFUP_FORUM_REGLEMENT_A_POSTERIORI'  , 4);

$AFUP_Tarifs_Forum = array(
                           AFUP_FORUM_INVITATION => 0,
                           AFUP_FORUM_ORGANISATION => 0,
                           AFUP_FORUM_SPONSOR => 0,
                           AFUP_FORUM_PRESSE => 0,
                           AFUP_FORUM_CONFERENCIER => 0,
                           AFUP_FORUM_PROJET => 0,
                           AFUP_FORUM_PROF => 0,
                           AFUP_FORUM_PREMIERE_JOURNEE => 120,
                           AFUP_FORUM_DEUXIEME_JOURNEE => 120,
                           AFUP_FORUM_2_JOURNEES       => 180,
                           AFUP_FORUM_2_JOURNEES_AFUP  => 120,
                           AFUP_FORUM_2_JOURNEES_ETUDIANT => 120,
                           AFUP_FORUM_2_JOURNEES_PREVENTE       => 160,
                           AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE  => 100,
                           AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => 100,
                           AFUP_FORUM_2_JOURNEES_COUPON => 140,
                           AFUP_FORUM_2_JOURNEES_SPONSOR => 180);

$AFUP_Tarifs_Forum_Lib = array(
                           AFUP_FORUM_INVITATION => 'Invitation',
                           AFUP_FORUM_ORGANISATION => 'Organisation',
                           AFUP_FORUM_PROJET => 'Projet PHP',
                           AFUP_FORUM_SPONSOR => 'Sponsor',
                           AFUP_FORUM_PRESSE => 'Presse',
                           AFUP_FORUM_PROF => 'Enseignement supérieur',
                           AFUP_FORUM_CONFERENCIER => 'Conferencier',
                           AFUP_FORUM_PREMIERE_JOURNEE => 'Jour 1 ',
                           AFUP_FORUM_DEUXIEME_JOURNEE => 'Jour 2',
                           AFUP_FORUM_2_JOURNEES       => '2 Jours',
                           AFUP_FORUM_2_JOURNEES_AFUP  =>  '2 Jours AFUP',
                           AFUP_FORUM_2_JOURNEES_ETUDIANT =>  '2 Jours Etudiant',
                           AFUP_FORUM_2_JOURNEES_PREVENTE       =>  '2 Jours prévente',
                           AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE  =>  '2 Jours AFUP prévente',
                           AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE =>  '2 Jours Etudiant prévente',
                           AFUP_FORUM_2_JOURNEES_COUPON =>  '2 Jours avec coupon de réduction',
                           AFUP_FORUM_2_JOURNEES_SPONSOR =>  '2 Jours par Sponsor');




class AFUP_Inscriptions_Forum
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
     * @param  object    $bdd   Instance de la couche d'abstraction ï¿½ la base de donnï¿½es
     * @access public
     * @return void
     */
    function AFUP_Inscriptions_Forum(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une inscription
     *$inscrits =
     * @param  int      $id         Identifiant de la personne
     * @param  string   $champs     Champs ï¿½ renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = 'i.*') {
        $requete  = 'SELECT';
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
     * @param $code_md5 Md5 de la concaténation des champs "id" et "reference"
     * @param string $champs Liste des champs à récupérer en BD
     * @return array
     */
    function obtenirInscription($code_md5, $champs = 'i.*') {
      $requete  = "SELECT $champs FROM afup_inscription_forum i " ;
      $requete .= "LEFT JOIN afup_facturation_forum f ON i.reference = f.reference " ;
      $requete .= "WHERE md5(CONCAT(i.id, i.reference)) = '$code_md5'" ;

      return $this->_bdd->obtenirEnregistrement($requete) ;
    }

    public function envoyerEmailConvocation($id_forum, $sujet, $corps) {
        require_once dirname(__FILE__).'/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        $requete  = 'SELECT';
        $requete .= '  i.*, f.societe, md5(CONCAT(i.id, i.reference)) as md5key ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        $requete .= 'ORDER BY i.date';
        $requete .= ' LIMIT 5000';
        $inscrits  = $this->_bdd->obtenirTous($requete);


        require_once dirname(__FILE__).'/../../dependencies/phpmailer/class.phpmailer.php';
        foreach ($inscrits as $nb => $inscrit) {
			if ($nb % 100 == 0) {
				sleep(5);
			}
			$mail = new PHPMailer();
			$mail->AddAddress($inscrit['email'], $inscrit['prenom'] . " " . $inscrit['nom']);

			$mail->From = $configuration->obtenir('mails|email_expediteur');
			$mail->FromName = $configuration->obtenir('mails|nom_expediteur');

			if ($configuration->obtenir('mails|serveur_smtp')) {
				$mail->Host = $configuration->obtenir('mails|serveur_smtp');
				$mail->Mailer = "smtp";
			} else {
				$mail->Mailer = "mail";
			}

			$mail->Subject = $sujet;

			$qui = $inscrit['prenom'].' '.$inscrit['nom'];
			$body = str_replace("%INSCRIT", $qui, $corps);

			$lien = "http://www.afup.org/pages/forumphp2010/convocation_visiteurs.php?id=".$inscrit['md5key'];
			$body = str_replace("%LIEN", $lien, $body);
			$mail->Body = $body;

			$ok = $mail->Send();
		}
		return $ok;
    }

    function obtenirSuivi($id_forum) {
    	$forum = new AFUP_Forum($this->_bdd);
    	$id_forum_precedent = $forum->obtenirPrecedent($id_forum);

    	$requete  = 'SELECT ';
    	$requete .= '  COUNT(*) as nombre, ';
    	$requete .= '  UNIX_TIMESTAMP(FROM_UNIXTIME(date, \'%Y-%m-%d\')) as jour, ';
    	$requete .= '  id_forum ';
    	$requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'WHERE ';
        $requete .= '  i.id_forum IN ('.(int)$id_forum.', '.(int)$id_forum_precedent.') ';
        $requete .= 'AND ';
        $requete .= '  etat <> 1 ';
        $requete .= 'GROUP BY jour, id_forum ';
        $requete .= 'ORDER BY jour DESC ';
        $nombre_par_date = $this->_bdd->obtenirTous($requete);

        foreach ($nombre_par_date as $nombre) {
        	$inscrits[$nombre['id_forum']][$nombre['jour']] = $nombre['nombre'];
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

    function obtenirListePourEmargement($id_forum = null) {
        $requete  = 'SELECT';
        $requete .= '  i.*, f.societe ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
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

    function obtenirListePourBadges($id_forum = null, $id = null) {
        $requete  = 'SELECT';
        $requete .= '  i.*, f.societe ';
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
     * @param  string   $champs         Champs ï¿½ renvoyer
     * @param  string   $ordre          Tri des enregistrements
     * @param  bool     $associatif     Renvoyer un tableau associatif ?
     * @access public
     * @return array
     */
    function obtenirListe($id_forum   = null,
                          $champs     = 'i.*',
                          $ordre      = 'i.date',
                          $associatif = false,
                          $filtre     = false)
    {
        $requete  = 'SELECT';
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
                                $newsletter_nexen, $commentaires =null,
                                $etat = AFUP_FORUM_ETAT_CREE, $facturation = AFUP_FORUM_FACTURE_A_ENVOYER)
    {
        $requete  = 'INSERT INTO ';
        $requete .= '  afup_inscription_forum (id_forum, date, reference, type_inscription, montant,
                               civilite, nom, prenom, email, telephone, coupon, citer_societe,
                               newsletter_afup, newsletter_nexen, commentaires, etat, facturation) ';
        $requete .= 'VALUES (';
        $requete .= $id_forum                                       . ',';
        $requete .= time()                                          . ',';
        $requete .= $this->_bdd->echapper($reference)               . ',';
        $requete .= $this->_bdd->echapper($type_inscription)        . ',';
        $requete .= $GLOBALS['AFUP_Tarifs_Forum'][$type_inscription]. ',';
        $requete .= $this->_bdd->echapper($civilite)                . ',';
        $requete .= $this->_bdd->echapper($nom)                     . ',';
        $requete .= $this->_bdd->echapper($prenom)                  . ',';
        $requete .= $this->_bdd->echapper($email)                   . ',';
        $requete .= $this->_bdd->echapper($telephone)               . ',';
        $requete .= $this->_bdd->echapper($coupon)                  . ',';
        $requete .= $this->_bdd->echapper($citer_societe)           . ',';
        $requete .= $this->_bdd->echapper($newsletter_afup)         . ',';
        $requete .= $this->_bdd->echapper($newsletter_nexen)        . ',';
        $requete .= $this->_bdd->echapper($commentaires)            . ',';
        $requete .= $etat                                           . ',';
        $requete .= $this->_bdd->echapper($facturation)             . ')';

        return $this->_bdd->executer($requete);
    }

    function modifierInscription($id, $reference, $type_inscription, $civilite, $nom, $prenom,
                                 $email, $telephone, $coupon, $citer_societe, $newsletter_afup,
                                 $newsletter_nexen, $commentaires, $etat, $facturation)
    {
        $requete  = 'UPDATE ';
        $requete .= '  afup_inscription_forum ';
        $requete .= 'SET';
        $requete .= '  reference='               . $this->_bdd->echapper($reference)               . ',';
        $requete .= '  type_inscription='        . $this->_bdd->echapper($type_inscription)        . ',';
        $requete .= '  montant='                 . $GLOBALS['AFUP_Tarifs_Forum'][$type_inscription]. ',';
        $requete .= '  civilite='                . $this->_bdd->echapper($civilite)                . ',';
        $requete .= '  nom='                     . $this->_bdd->echapper($nom)                     . ',';
        $requete .= '  prenom='                  . $this->_bdd->echapper($prenom)                  . ',';
        $requete .= '  email='                   . $this->_bdd->echapper($email)                   . ',';
        $requete .= '  telephone='               . $this->_bdd->echapper($telephone)               . ',';
        $requete .= '  coupon='                  . $this->_bdd->echapper($coupon)                  . ',';
        $requete .= '  citer_societe='           . $this->_bdd->echapper($citer_societe)           . ',';
        $requete .= '  newsletter_afup='         . $this->_bdd->echapper($newsletter_afup)         . ',';
        $requete .= '  newsletter_nexen='        . $this->_bdd->echapper($newsletter_nexen)        . ',';
        $requete .= '  commentaires='            . $this->_bdd->echapper($commentaires)            . ',';
        $requete .= '  etat='                    . $this->_bdd->echapper($etat)                    . ',';
        $requete .= '  facturation='             . $this->_bdd->echapper($facturation)                    . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

		$this->modifierEtatInscription($reference, $etat);

        return $this->_bdd->executer($requete);
    }

	function supprimerInscription($id) {
		$requete = 'DELETE FROM afup_inscription_forum WHERE id=' . $id;
		return $this->_bdd->executer($requete);
	}

	function modifierEtatInscription($reference, $etat)
    {
        $requete   = 'UPDATE afup_inscription_forum ';
        $requete  .= 'SET etat=' . $etat . ' ';
        $requete  .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        $this->_bdd->executer($requete);

        $requete   = 'UPDATE afup_facturation_forum ';
        $requete  .= 'SET etat=' . $etat . ' ';
        $requete  .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->executer($requete);
    }

	function ajouterRappel($email, $id_forum = null)
    {
        if ($id_forum == null) {
			require_once dirname(__FILE__).'/AFUP_Forum.php';
			$forum = new AFUP_Forum($this->_bdd);
        	$id_forum = $forum->obtenirDernier();
        }
        $requete   = 'INSERT INTO afup_inscriptions_rappels (email, date, id_forum) VALUES (' . $this->_bdd->echapper($email) . ', ' . time() . ', ' . $id_forum . ')';
        return $this->_bdd->executer($requete);
    }

	function obtenirNombreInscrits($id_forum   = null)
	{
        $statistiques  = $this->obtenirStatistiques($id_forum);
		$nombresInscrits = max($statistiques['premier_jour']['inscrits'], $statistiques['second_jour']['inscrits']);

		return $nombresInscrits;
	}

    function obtenirStatistiques($id_forum   = null)
    {
        $statistiques = array();

        // Premier jour
        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['premier_jour']['inscrits'] = $this->_bdd->obtenirUn($requete);

        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_INVITE . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['premier_jour']['confirmes'] = $this->_bdd->obtenirUn($requete);

        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat = ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ' ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_PREMIERE_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['premier_jour']['en_attente_de_reglement'] = $this->_bdd->obtenirUn($requete);

        // Second jour
        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['second_jour']['inscrits'] = $this->_bdd->obtenirUn($requete);

        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_INVITE  . ', ' . AFUP_FORUM_ETAT_CONFIRME . ') ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['second_jour']['confirmes'] = $this->_bdd->obtenirUn($requete);

        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat = ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ' ';
        $requete .= '  AND type_inscription IN (' . AFUP_FORUM_DEUXIEME_JOURNEE . ',' . AFUP_FORUM_2_JOURNEES . ',' . AFUP_FORUM_2_JOURNEES_AFUP . ',' . AFUP_FORUM_2_JOURNEES_ETUDIANT . ')';
        $statistiques['second_jour']['en_attente_de_reglement'] = $this->_bdd->obtenirUn($requete);

        // Nombre de personnes validï¿½es par type d'inscription
        $requete  = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ', ' . AFUP_FORUM_ETAT_INVITE . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['confirmes'] = $this->_bdd->obtenirAssociatif($requete);

        $requete  = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['payants'] = $this->_bdd->obtenirAssociatif($requete);

        $requete  = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= 'AND etat NOT IN (' . AFUP_FORUM_ETAT_ANNULE . ', ' . AFUP_FORUM_ETAT_ERREUR . ', ' . AFUP_FORUM_ETAT_REFUSE . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['inscrits'] = $this->_bdd->obtenirAssociatif($requete);

        $requete  = 'SELECT type_inscription, COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
        $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY type_inscription';
        $statistiques['types_inscriptions']['payants'] = $this->_bdd->obtenirAssociatif($requete);

        $requete  = 'SELECT concat(type_inscription,\'-\',etat) , COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
       // $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY concat(type_inscription,\'-\',etat)';
        //$statistiques['types_inscriptions']['etat'] = $this->_bdd->obtenirAssociatif($requete);

        $requete  = 'SELECT COUNT(*) ';
        $requete .= 'FROM afup_inscription_forum ';
        $requete .= 'WHERE id_forum =' . $id_forum . ' ';
       // $requete .= '  AND etat IN (' . AFUP_FORUM_ETAT_REGLE . ', ' . AFUP_FORUM_ETAT_ATTENTE_REGLEMENT . ') ';
        $requete .= 'GROUP BY id_forum';
        //$statistiques['types_inscriptions']['total'] = $this->_bdd->obtenirUn($requete);

        return $statistiques;
    }
}
?>