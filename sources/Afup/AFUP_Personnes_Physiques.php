<?php

require_once 'Afup/AFUP_Droits.php';

// Voir la classe AFUP_Personnes_Morales
define('AFUP_PERSONNES_PHYSIQUES',          0);
define('AFUP_COTISATION_PERSONNE_PHYSIQUE', 25);

/**
 * Classe de gestion des personnes physiques
 */
class AFUP_Personnes_Physiques {
    /**
     * Instance de la couche d'abstraction à la base de données
     *
     * @var object
     * @access private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function AFUP_Personnes_Physiques(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit la liste des personnes physiques
     *
     * @param string $champs Champs à renvoyer
     * @param string $ordre Tri des enregistrements
     * @access public
     * @return array
     */
    function obtenirListe($champs = '*',
        $ordre = 'nom, prenom',
        $filtre = false,
        $id_personne_morale = false,
        $associatif = false,
        $id_personne_physique = false,
        $is_active = NULL)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE 1 = 1 ';
        if ($filtre) {
            $filtre = $this->_bdd->echapper('%'.$filtre.'%');
            $requete .= 'AND (nom LIKE ' . $filtre . ' ';
            $requete .= 'OR login LIKE ' . $filtre . ' ';
            $requete .= 'OR prenom LIKE ' . $filtre . ' ';
            $requete .= 'OR code_postal LIKE ' . $filtre . ' ';
            $requete .= 'OR ville LIKE ' . $filtre . ') ';
        }
        if ($id_personne_morale) {
            $requete .= 'AND id_personne_morale = ' . $id_personne_morale . ' ';
        }
        if ($id_personne_physique) {
            if (!is_array($id_personne_physique)) {
                $id_personne_physique = array($id_personne_physique);
            }
            $requete .= 'AND id IN (' . join(",", $id_personne_physique) . ') ';
        }
        if ($is_active !== NULL) {
        	$requete .= 'AND etat = ' . $is_active . ' ';
        }
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    /**
     * Renvoit les informations concernant une personne physique
     *
     * @param int $id Identifiant de la personne
     * @param string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE id=' . $id;
        $resultats = $this->_bdd->obtenirEnregistrement($requete);

		/*On redivise les droits sur les modules*/
		if (isset($resultats['niveau_modules'][0])) {
			$resultats['niveau_apero'] = $resultats['niveau_modules'][0];
		}
		if (isset($resultats['niveau_modules'][1])) {
			$resultats['niveau_annuaire'] = $resultats['niveau_modules'][1];
		}
		if (isset($resultats['niveau_modules'][2])) {
			$resultats['niveau_site'] = $resultats['niveau_modules'][2];
		}
		if (isset($resultats['niveau_modules'][3])) {
			$resultats['niveau_forum'] = $resultats['niveau_modules'][3];
		}
		unset($resultats["niveau_modules"]);

		return $resultats;
    }

    /**
     * Ajoute une personne physique
     *
     * @param  int      $id_personne_morale     Identifiant de la personne morale à laquelle est liée la personne physique
     * @param  string   $login                  Login de la personne physique
     * @param  string   $mot_de_passe           Mot de passe de la personne physique
     * @param  int      $niveau                 Niveau de droits de la personne physique
	 * @param  string   $niveau_modules         Niveau de droits sur les différents modules de la personne physique
     * @param  string   $civilite               Civilité de la personne physique
     * @param  string   $nom                    Nom de la personne physique
     * @param  string   $prenom                 Prénom de la personne physique
     * @param  string   $email                  Email de la personne physique
     * @param  string   $adresse                Adresse de la personne physique
     * @param  string   $code_postal            Code postal de la personne physique
     * @param  string   $ville                  Ville de la personne physique
     * @param  int      $id_pays                Identifiant du pays de la personne physique
     * @param  string   $telephone_fixe         Téléphone fixe de la personne physique
     * @param  string   $telephone_portable     Téléphone portable de la personne physique
     * @param  int      $etat                   Etat de la personne physique
     * @access public
     * @return bool Succès de l'ajout
     */
    function ajouter($id_personne_morale, $login, $mot_de_passe, $niveau, $niveau_modules, $civilite, $nom, $prenom,
        $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $compte_svn)
    {
        if (empty($id_personne_morale)) {
            $id_personne_morale = null;
        }

        $erreur = false;

        $erreur = $erreur || !$this->_controleAbsenceLogin(0, $login);
    	$erreur = $erreur || !$this->_controleExistancePersonneMorale($id_personne_morale);
        $erreur = $erreur || !$this->_controleExistancePays($id_pays);

        if (!$erreur) {
            $requete = 'INSERT INTO ';
            $requete .= '  afup_personnes_physiques (id_personne_morale, login, mot_de_passe, niveau, niveau_modules, civilite, nom, prenom, email, ';
            $requete .= '  adresse, code_postal, ville, id_pays, telephone_fixe, telephone_portable, etat, compte_svn) ';
            $requete .= 'VALUES (';
            $requete .= (int)$id_personne_morale                   . ',';
            $requete .= $this->_bdd->echapper($login)              . ',';
            $requete .= $this->_bdd->echapper($mot_de_passe)       . ',';
            $requete .= (int)$niveau                               . ',';
			$requete .= $this->_bdd->echapper($niveau_modules)     . ',';
            $requete .= $this->_bdd->echapper($civilite)           . ',';
            $requete .= $this->_bdd->echapper($nom)                . ',';
            $requete .= $this->_bdd->echapper($prenom)             . ',';
            $requete .= $this->_bdd->echapper($email)              . ',';
            $requete .= $this->_bdd->echapper($adresse)            . ',';
            $requete .= $this->_bdd->echapper($code_postal)        . ',';
            $requete .= $this->_bdd->echapper($ville)              . ',';
            $requete .= $this->_bdd->echapper($id_pays)            . ',';
            $requete .= $this->_bdd->echapper($telephone_fixe)     . ',';
            $requete .= $this->_bdd->echapper($telephone_portable) . ',';
            $requete .= (int)$etat                                 . ',';
            $requete .= $this->_bdd->echapper($compte_svn)          . ')';

            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Modifie une personne physique
     *
     * @param  int      $id                     Identifiant de la personne physique
     * @param  int      $id_personne_morale     Identifiant de la personne morale à laquelle est liée la personne physique
     * @param  string   $login                  Login de la personne physique
     * @param  string   $mot_de_passe           Mot de passe de la personne physique
     * @param  int      $niveau                 Niveau de droits de la personne physique
	 * @param  string   $niveau_modules         Niveau de droits sur les différents modules de la personne physique
     * @param  string   $civilite               Civilité de la personne physique
     * @param  string   $nom                    Nom de la personne physique
     * @param  string   $prenom                 Prénom de la personne physique
     * @param  string   $email                  Email de la personne physique
     * @param  string   $adresse                Adresse de la personne physique
     * @param  string   $code_postal            Code postal de la personne physique
     * @param  string   $ville                  Ville de la personne physique
     * @param  int      $id_pays                Identifiant du pays de la personne physique
     * @param  string   $telephone_fixe         Téléphone fixe de la personne physique
     * @param  string   $telephone_portable     Téléphone portable de la personne physique
     * @param  int      $etat                   Etat de la personne physique
     * @access public
     * @return bool Succès de la modification
     */
    function modifier($id, $id_personne_morale, $login, $mot_de_passe, $niveau, $niveau_modules, $civilite, $nom, $prenom,
        $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable, $etat, $compte_svn)
    {
        $erreur = false;

        $erreur = $erreur || !$this->_controleAbsenceLogin($id, $login);
        $erreur = $erreur || !$this->_controleExistancePersonneMorale($id_personne_morale);
        $erreur = $erreur || !$this->_controleExistancePays($id_pays);

        if (!$erreur) {
            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';

            if (!empty($mot_de_passe)) {
                $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe)) . ',';
            }

            $requete .= '  id_personne_morale=' . (int)$id_personne_morale                   . ',';
            $requete .= '  login='              . $this->_bdd->echapper($login)              . ',';
            $requete .= '  niveau='             . $this->_bdd->echapper($niveau)             . ',';
			$requete .= '  niveau_modules='     . $this->_bdd->echapper($niveau_modules)     . ',';
            $requete .= '  civilite='           . $this->_bdd->echapper($civilite)           . ',';
            $requete .= '  nom='                . $this->_bdd->echapper($nom)                . ',';
            $requete .= '  prenom='             . $this->_bdd->echapper($prenom)             . ',';
            $requete .= '  email='              . $this->_bdd->echapper($email)              . ',';
            $requete .= '  adresse='            . $this->_bdd->echapper($adresse)            . ',';
            $requete .= '  code_postal='        . $this->_bdd->echapper($code_postal)        . ',';
            $requete .= '  ville='              . $this->_bdd->echapper($ville)              . ',';
            $requete .= '  id_pays='            . $this->_bdd->echapper($id_pays)            . ',';
            $requete .= '  telephone_fixe='     . $this->_bdd->echapper($telephone_fixe)     . ',';
            $requete .= '  telephone_portable=' . $this->_bdd->echapper($telephone_portable) . ',';
            $requete .= '  etat='               . $this->_bdd->echapper($etat)               . ',';
            $requete .= '  compte_svn='         . $this->_bdd->echapper($compte_svn)         . ' ';
            $requete .= 'WHERE';
            $requete .= '  id=' . $id;

            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Modifie seulement les coordonnées d'une personne physique
     *
     * @param int $id Identifiant de la personne physique
     * @param string $login Login de la personne physique
     * @param string $mot_de_passe Mot de passe de la personne physique
     * @param string $email Email de la personne physique
     * @param string $adresse Adresse de la personne physique
     * @param string $code_postal Code postal de la personne physique
     * @param string $ville Ville de la personne physique
     * @param int $id_pays Identifiant du pays de la personne physique
     * @param string $telephone_fixe Téléphone fixe de la personne physique
     * @param string $telephone_portable Téléphone portable de la personne physique
     * @access public
     * @return bool Succès de la modification
     */
    function modifierCoordonnees($id, $login, $mot_de_passe,
        $email, $adresse, $code_postal, $ville, $id_pays, $telephone_fixe, $telephone_portable)
    {
        $erreur = false;

        $erreur = $erreur || !$this->_controleAbsenceLogin($id, $login);
        $erreur = $erreur || !$this->_controleExistancePays($id_pays);

        if (!$erreur) {
            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';

            if (!empty($mot_de_passe)) {
                $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe)) . ',';
            }
            $requete .= '  login=' . $this->_bdd->echapper($login) . ',';
            $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
            $requete .= '  adresse=' . $this->_bdd->echapper($adresse) . ',';
            $requete .= '  code_postal=' . $this->_bdd->echapper($code_postal) . ',';
            $requete .= '  ville=' . $this->_bdd->echapper($ville) . ',';
            $requete .= '  id_pays=' . $this->_bdd->echapper($id_pays) . ',';
            $requete .= '  telephone_fixe=' . $this->_bdd->echapper($telephone_fixe) . ',';
            $requete .= '  telephone_portable=' . $this->_bdd->echapper($telephone_portable);
            $requete .= 'WHERE';
            $requete .= '  id=' . $id;
            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Envoi un nouveau mot de passe lorsque l'utilisateur le demande
     *
     * @param string $login Login de la personne physique
     * @param string $email Email de la personne physique
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerMotDePasse($login, $email, $id = null)
    {
        $succes = false;

        $selection = 'SELECT ';
        $selection .= ' id, login, email ';
        $selection .= 'FROM ';
        $selection .= '  afup_personnes_physiques ';
        $selection .= 'WHERE ';
        if ($id === null) {
            $selection .= '  email=' . $this->_bdd->echapper($email);
        } else {
            $selection .= '  id=' . $this->_bdd->echapper($id) . ' ';
        }
        $data = $this->_bdd->obtenirEnregistrement($selection);
        $id = $data['id'];
        $identifiant = $data['login'];
        $email = $data['email'];
        if (is_numeric($id) and $id > 0) {
            $mot_de_passe = substr(md5(uniqid(rand(), true)), 0, 10);

            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';
            $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe));
            $requete .= 'WHERE';
            $requete .= '  id=' . $this->_bdd->echapper($id);

            if ($this->_bdd->executer($requete)) {
                $corps = "Votre nouveau mot de passe est indiqué ci-dessous. \n";
                $corps .= "Il vous permettra de vous connecter dès maintenant au site de l'AFUP.\n\n";
                $corps .= "Votre identifiant : $identifiant \n";
                $corps .= "Votre mot de passe : $mot_de_passe \n\n";
                $corps .= "http://www.afup.org/pages/administration/index.php?ctx_login=$identifiant";


                $check = AFUP_Mailing::envoyerMail(
                            $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                            $email,
                            "AFUP : Mot de passe perdu ?",
                            $corps);

                return($check);

            }
        }

        return $succes;
    }

    /**
     * Envoi un message de bienvenue lorsque l'utilisateur le demande
     *
     * @param string $login Login de la personne physique
     * @param string $email Email de la personne physique
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerCourrierBienvenue($login, $email, $id = null)
    {
        $succes = false;

        $selection = 'SELECT ';
        $selection .= ' id, login, email ';
        $selection .= 'FROM ';
        $selection .= '  afup_personnes_physiques ';
        $selection .= 'WHERE ';
        if ($id === null) {
            $selection .= '  email=' . $this->_bdd->echapper($email);
        } else {
            $selection .= '  id=' . $this->_bdd->echapper($id) . ' ';
        }
        $data = $this->_bdd->obtenirEnregistrement($selection);
        $id = $data['id'];
        $identifiant = $data['login'];
        $email = $data['email'];
        if (is_numeric($id) and $id > 0) {
            $mot_de_passe = substr(md5(uniqid(rand(), true)), 0, 10);

            $requete = 'UPDATE ';
            $requete .= '  afup_personnes_physiques ';
            $requete .= 'SET';
            $requete .= '  mot_de_passe=' . $this->_bdd->echapper(md5($mot_de_passe));
            $requete .= 'WHERE';
            $requete .= '  id=' . $this->_bdd->echapper($id);

            if ($this->_bdd->executer($requete)) {

                $corps = "Cher membre,\n\n";
                $corps .= "Tout d'abord bienvenue à l'AFUP ! Nous sommes ravis de vous accueillir au sein de l'Association Française des Utilisateurs de PHP, réunissant tous les utilisateurs de la plate-forme PHP.\n\n";
                $corps .= "Être membre de l'AFUP vous ouvre la porte à une multitude d'avantages:\n";
                $corps .= "- un accès au back-office, qui vous permettra de nous aider à nourrir les projets. Voici votre identifiant : $identifiant . Votre mot de passe est : $mot_de_passe. Le back-office est accessible à cette adresse: www.afup.org/wiki . Vous pouvez changer votre mot de passe en vous connectant sur la page « administration » de l'AFUP : www.afup.org/pages/administration.\n";
                $corps .= "- des invitations aux Rendez-vous AFUP : conférences thématiques, à Paris ou en province, animées par des experts. Le coût de ces conférences étant pris en charge par l'AFUP, elles sont gratuites pour les participants!\n";
                $corps .= "- des Apéros PHP: des développeurs se réunissent régulièrement autour d'un verre pour discuter PHP. Soyez tenu au courant de ces apéros, participez à ceux qui se déroulent dans votre ville, rencontrez des développeurs PHP près de chez vous. \n";
                $corps .= "- un tarif préférentiel au Forum PHP et au PHP Tour: votre adhésion à l'AFUP vous donnera droit au tarif « membre AFUP » lors de ces prochains événements.\n";
                $corps .= "- des réductions exclusives négociées auprès de nos partenaires sur leurs formations et leurs services : découvrez les http://afup.org/wiki/wakka.php?wiki=OffreDesPartenaires\n";
                $corps .= "- l'inscription à la mailing-list des membres de l'AFUP: un problème de code ? Besoin d'un conseil ? Une offre d'emploi à diffuser ? Une actualité qui pourrait intéresser la communauté ? Ecrivez à la mailing-list membres@afup.org et échangez avec les centaines de membres de l'AFUP.\n\n";
                $corps .= "L'AFUP, c'est aussi un site web à optimiser, des outils à (ré)inventer, un forum à organiser, des Rendez-vous AFUP à suggérer: l'AFUP a besoin de vous pour avancer !\n";
                $corps .= "- Proposez-nous vos thématiques pour les Rendez-vous AFUP. Vous avez envie d'inviter un conférencier ? Vous avez besoin de louer une salle ? Vous souhaitez contacter les membres AFUP de votre ville ? Contactez-nous, nous vous aiderons à mettre en place votre soirée PHP grâce à un soutien logistique et financier.\n";
                $corps .= "- Participez à la création d'outils pour l'AFUP. Le site de l'AFUP va être repensé, le site des Apéros PHP va entrer en chantier, de nouveaux outils de communication vont être développés. Aidez-nous à les mettre en œuvre !\n";
                $corps .= "- Lancez des Apéros PHP dans votre ville: de nombreux membres de l'AFUP habitent certainement près de chez vous, l'AFUP peut vous aider à vous mettre en contact.\n\n";
                $corps .= "Enfin, adhérer à l'AFUP, c'est surtout soutenir la plate-forme et montrer son envie de la voir grandir et progresser.\n\n";
                $corps .= "A très bientôt,\n\n";
                $corps .= "L'équipe AFUP\n";

                $check = AFUP_Mailing::envoyerMail(
                            $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                            $email,
                            "Adhésion AFUP",
                            $corps);

                return($check);

            }
        }

        return $succes;
    }

    /**
     * Supprime une personne physique
     *
     * @param int $id Identifiant de la personne physique
     * @access public
     * @return bool Succès de la suppresion
     */
    function supprimer($id)
    {
        require_once 'Afup/AFUP_Cotisations.php';
        $cotisation = new AFUP_Cotisations($this->_bdd);
        $cotisation_personne_physisque = $cotisation->obtenirListe(AFUP_PERSONNES_PHYSIQUES, $id, 'id');

        if (sizeof($cotisation_personne_physisque) == 0) {
            $requete = 'DELETE FROM afup_personnes_physiques WHERE id=' . $id;
            return $this->_bdd->executer($requete);
        }

        return false;
    }

    /**
     * Renvoie la dernière cotisation
     *
     * @param int $id Identifiant de la personne physique
     * @access public
     * @return array
     */
	function obtenirDerniereCotisation($id_personne_physique)
	{
		require_once 'Afup/AFUP_Cotisations.php';

        $requete  = 'SELECT';
        $requete .= '  id_personne_morale ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id = '.$id_personne_physique;
		$id_personne_morale = $this->_bdd->obtenirUn($requete);

		$cotisation = new AFUP_Cotisations($this->_bdd);
		if ($id_personne_morale > 0) {
			$id = $id_personne_morale;
			$type_personne = AFUP_PERSONNES_MORALES;
		} else {
			$id = $id_personne_physique;
			$type_personne = AFUP_PERSONNES_PHYSIQUES;
		}

		return $cotisation->obtenirDerniere($type_personne, $id);
	}

    /**
     * Retourne le nombre de membres (personnes physiques qu'elles soient ou non
     * liées à une personne morale).
     *
     * @param int $etat Etat des membres à retourner
     * @access public
     * @return int
     */
    function obtenirNombreMembres($etat = null)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';

        if (!is_null($etat)) {
            $requete .= 'WHERE';
            $requete .= '  etat=' . $etat;
        }

        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Retourne le nombre de personnes physiques non liées à une personne
     * morale.
     *
     * @param int $etat Etat des personnes à retourner
     * @access public
     * @return int
     */
    function obtenirNombrePersonnesPhysiques($etat = null)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id_personne_morale = 0';

        if (!is_null($etat)) {
            $requete .= '  AND etat=' . $etat;
        }

        return $this->_bdd->obtenirUn($requete);
    }

    /**
     * Contrôle si le login n'est pas déja utilisé
     * morale.
     *
     * @param int $id Identifiant de la personne physique
     * @param int $id_personne_morale Identifiant de la personne morale à laquelle est liée la personne physique
     * @param string $login Login de la personne physique
     * @access public
     * @return bool login non utilisé
     */
    function _controleAbsenceLogin($id, $login)
    {
        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_personnes_physiques ';
        $requete .= 'WHERE login=' . $this->_bdd->echapper($login) . ' AND id <> ' . intval($id);

        return ($this->_bdd->obtenirUn($requete) === false);
    }

    /**
     * Contrôle si l'id_personne_morale existe
     * morale.
     *
     * @param int $id_personne_morale Identifiant de la personne morale à laquelle est liée la personne physique
     * @access public
     * @return bool login non utilisé
     */
    function _controleExistancePersonneMorale($id_personne_morale)
    {
        if (!isset($id_personne_morale) || empty($id_personne_morale)) return true;

        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_personnes_morales ';
        $requete .= 'WHERE id = ' . intval($id_personne_morale);

        return ($this->_bdd->obtenirUn($requete) !== false);
    }

    /**
     * Contrôle si l'id_pays existe
     * morale.
     *
     * @param int $id_pays Identifiant du pays
     * @access public
     * @return bool login non utilisé
     */
    function _controleExistancePays($id_pays)
    {
        if ($id_pays == 0) {
            return true;
        }

        $requete = 'SELECT 1 ';
        $requete .= 'FROM afup_pays ';
        $requete .= 'WHERE id = ' . intval($id_pays);

        return ($this->_bdd->obtenirUn($requete) !== false);
    }

    function obtenirIdDepuisCompteSVN($compte_svn)
    {
        $requete  = ' SELECT id ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE compte_svn = '.$this->_bdd->echapper($compte_svn);

        return $this->_bdd->obtenirUn($requete);
    }

    public function getListeAvecDroitsAdministration()
    {
        $requete  = ' SELECT * ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE niveau_modules <> 0 ';
        $requete .= ' OR niveau = 2 ';
        $requete .= ' ORDER BY nom, prenom ';

        $resultats = $this->_bdd->obtenirTous($requete);

        foreach ($resultats as &$r) {
            /*On redivise les droits sur les modules*/
            if (isset($r['niveau_modules'][0])) {
                $r['niveau_apero'] = $r['niveau_modules'][0];
            }
            if (isset($r['niveau_modules'][1])) {
                $r['niveau_annuaire'] = $r['niveau_modules'][1];
            }
            if (isset($r['niveau_modules'][2])) {
                $r['niveau_site'] = $r['niveau_modules'][2];
            }
            if (isset($r['niveau_modules'][3])) {
                $r['niveau_forum'] = $r['niveau_modules'][3];
            }
            if (isset($r['niveau_modules'][4])) {
                $r['niveau_antenne'] = $r['niveau_modules'][4];
            }
            unset($r["niveau_modules"]);
        }

        return $resultats;
    }

    public function getUserByEmail($email)
    {
        $requete  = ' SELECT * ';
        $requete .= ' FROM afup_personnes_physiques ';
        $requete .= ' WHERE email = '.$this->_bdd->echapper($email);
        return $this->_bdd->obtenirEnregistrement($requete);
    }
}
