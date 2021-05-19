<?php
namespace Afup\Site\Corporate;



class Feuilles
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    protected $bdd;

    function __construct($bdd = false)
    {
        if ($bdd) {
            $this->bdd = $bdd;
        } else {
            $this->bdd = new _Site_Base_De_Donnees();
        }
    }

    function obtenirListe($champs = '*', $ordre = 'titre', $associatif = false)
    {
        $requete = 'SELECT ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_site_feuille ';
        $requete .= 'ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->bdd->obtenirAssociatif($requete);
        }
        return $this->bdd->obtenirTous($requete);
    }
}