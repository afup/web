<?php
class AFUP_Forum_Partenaires
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
    function AFUP_Forum_Partenaires(&$bdd)
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
        $requete .= '  afup_forum_partenaires ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirTousPartenairesForum($id_forum)
    {
        $requete  = 'SELECT';
        $requete .= '  *  ';
        $requete .= 'FROM';
        $requete .= '  afup_forum_partenaires ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_partenaires ON afup_partenaires.id = afup_forum_partenaires.id_partenaire ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_niveau_partenariat ON afup_niveau_partenariat.id = afup_forum_partenaires.id_niveau_partenariat ';
        $requete .= 'WHERE';
        $requete .= ' id_forum=' . $id_forum . ' ';
        $requete .= 'ORDER BY';
        $requete .= ' id_niveau_partenariat, ranking';
        $partenaires = $this->_bdd->obtenirTous($requete);
        foreach($partenaires as $p) {
            $parType[$p['titre']][] = $p;
        }
        return $parType;
    }
}