<?php

class AFUP_Accreditation_Presse
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
     * @param  object    $bdd   Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function AFUP_Accreditation_Presse(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une accreditation
     *
     * @param  string   $reference  Id de l'accréditation
     * @param  string   $champs     Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($reference, $champs = '*')
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_accreditation_presse ';
        $requete .= 'WHERE id=' . $this->_bdd->echapper($reference);
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirListe($ordre = 'date DESC', $associatif = false)
    {
		$requete = 'SELECT';
		$requete .= '  afup_accreditation_presse.*, afup_forum.titre as nom_forum';
		$requete .= ' FROM';
		$requete .= '  afup_accreditation_presse';
		$requete .= ' INNER JOIN';
		$requete .= '  afup_forum ON afup_forum.id=afup_accreditation_presse.id_forum';
		$requete .= ' ORDER BY '.$ordre;

		if ($associatif) {
			return $this->_bdd->obtenirAssociatif($requete);
		} else {
			return $this->_bdd->obtenirTous($requete);
		}
	}

    /**
     * Ajout d'une demande d'accréditation presse
     *
     * @param type $id
     * @param type $date
     * @param type $titre_revue
     * @param type $civilite
     * @param type $nom
     * @param type $prenom
     * @param type $carte_presse
     * @param type $adresse
     * @param type $code_postal
     * @param type $ville
     * @param type $id_pays
     * @param type $telephone
     * @param type $email
     * @param type $commentaires
     * @param type $id_forum
     * @param type $valide
     * @return boolean
     */
    function ajouter($id, $date, $titre_revue, $civilite, $nom, $prenom, $carte_presse,
                     $adresse, $code_postal, $ville, $id_pays, $telephone, $email,
                     $commentaires, $id_forum, $valide)
    {
        $erreur = false;

        $erreur = $erreur || !$this->_controleExistancePays($id_pays);

        if (!$erreur) {
            $requete = 'INSERT INTO ';
            $requete .= '  afup_accreditation_presse (id, date, titre_revue, civilite, nom, prenom,';
            $requete .= ' carte_presse, adresse, code_postal, ville, id_pays, telephone, email,';
            $requete .= ' commentaires, id_forum, valide) ';
            $requete .= 'VALUES (';
            $requete .= $this->_bdd->echapper($id)                 . ',';
            $requete .= $this->_bdd->echapper($date)               . ',';
            $requete .= $this->_bdd->echapper($titre_revue)        . ',';
            $requete .= $this->_bdd->echapper($civilite)           . ',';
            $requete .= $this->_bdd->echapper($nom)                . ',';
            $requete .= $this->_bdd->echapper($prenom)             . ',';
            $requete .= $this->_bdd->echapper($carte_presse)       . ',';
            $requete .= $this->_bdd->echapper($adresse)            . ',';
            $requete .= $this->_bdd->echapper($code_postal)        . ',';
            $requete .= $this->_bdd->echapper($ville)              . ',';
            $requete .= $this->_bdd->echapper($id_pays)            . ',';
            $requete .= $this->_bdd->echapper($telephone)          . ',';
            $requete .= $this->_bdd->echapper($email)              . ',';
            $requete .= $this->_bdd->echapper($commentaires)       . ',';
            $requete .= (int)$id_forum                             . ',';
            $requete .= (int)$valide                               . ')';

            return $this->_bdd->executer($requete);
        }

        return false;
    }

    function modifier($id, $titre_revue, $civilite, $nom, $prenom, $carte_presse,
                     $adresse, $code_postal, $ville, $id_pays, $telephone, $email,
                     $commentaires, $id_forum, $valide)
    {
        $requete  = 'UPDATE ';
        $requete .= '  afup_accreditation_presse ';
        $requete .= 'SET';
        $requete .= '  titre_revue='             . $this->_bdd->echapper($titre_revue)             . ',';
        $requete .= '  civilite='                . $this->_bdd->echapper($civilite)                . ',';
        $requete .= '  nom='                     . $this->_bdd->echapper($nom)                     . ',';
        $requete .= '  prenom='                  . $this->_bdd->echapper($prenom)                  . ',';
        $requete .= '  carte_presse='            . $this->_bdd->echapper($carte_presse)            . ',';
        $requete .= '  adresse='                 . $this->_bdd->echapper($adresse)                 . ',';
        $requete .= '  code_postal='             . $this->_bdd->echapper($code_postal)             . ',';
        $requete .= '  ville='                   . $this->_bdd->echapper($ville)                   . ',';
        $requete .= '  id_pays='                 . $this->_bdd->echapper($id_pays)                 . ',';
        $requete .= '  telephone='               . $this->_bdd->echapper($telephone)               . ',';
        $requete .= '  email='                   . $this->_bdd->echapper($email)                   . ',';
        $requete .= '  commentaires='            . $this->_bdd->echapper($commentaires)            . ',';
        $requete .= '  id_forum='                . $this->_bdd->echapper($id_forum)                . ',';
        $requete .= '  valide='                  . $this->_bdd->echapper($valide)                  . ' ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $id;

        return $this->_bdd->executer($requete);
    }

	function supprimer($id) {
		$requete = 'DELETE FROM afup_accreditation_presse WHERE id=' . $id;
		return $this->_bdd->executer($requete);
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
}