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