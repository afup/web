<?php

class AFUP_BlackList
{
	var $_bdd;

	function AFUP_BlackList(& $bdd)
	{
		$this->_bdd = $bdd;
	}

    function blackList($mail)
    {
        if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $requete =  'INSERT INTO afup_blacklist VALUES (null, ';
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