<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Feuilles
{
    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public function obtenirListe(string $champs = '*', string $ordre = 'titre', $associatif = false)
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
