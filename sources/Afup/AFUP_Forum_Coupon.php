<?php
class AFUP_Forum_Coupon
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
    function AFUP_Forum_Coupon(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant un forum
     *
     * @param  int      $id         Identifiant du forum
     * @param  string   $champs     Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenir($id, $champs = '*')
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_coupon ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirCouponsForum($id_forum)
    {
        $requete  = 'SELECT';
        $requete .= '  texte, texte ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_coupon ';
        $requete .= 'WHERE id_forum=' . $id_forum;
        return array_values($this->_bdd->obtenirAssociatif($requete));
    }
}