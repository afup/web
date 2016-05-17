<?php

namespace Afup\Site;

class BlackList
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    var $_bdd;

    function __construct(& $bdd)
    {
        $this->_bdd = $bdd;
    }

    function blackList($mail)
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $requete = 'INSERT INTO afup_blacklist VALUES (NULL, ';
            $requete .= $this->_bdd->echapper($mail) . ')';
            $this->_bdd->executer($requete);
        }
    }

    function obtenirListe()
    {
        $requete = 'SELECT';
        $requete .= '  id, email';
        $requete .= ' FROM';
        $requete .= '  afup_blacklist';
        return $this->_bdd->obtenirAssociatif($requete);
    }
}