<?php
class AFUP_Niveau_Partenariat
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
    function AFUP_Niveau_Partenariat(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit la liste des types de partenariat
     *
     * @return array
     */
    function obtenirListe()
    {
        $requete  = 'SELECT';
        $requete .= '  id, titre ';
        $requete .= 'FROM';
        $requete .= '  afup_niveau_partenariat ';
        $requete .= 'ORDER BY id';
        return $this->_bdd->obtenirAssociatif($requete);
    }
}