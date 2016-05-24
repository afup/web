<?php
namespace Afup\Site\Aperos;
class Villes
{

	/**
	 * @var \Afup\Site\Utils\Base_De_Donnees
	 */
    private $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function obtenirListe($ordre = 'nom ASC', $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  *';
        $requete .= ' FROM';
        $requete .= '  afup_aperos_villes';
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }
}