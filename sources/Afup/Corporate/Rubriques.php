<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Rubriques
{
    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    /**
     * @return Rubrique[]
     */
    public function chargerSousRubriques($id_site_rubrique): array
    {
        $requete = ' SELECT';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_site_rubrique ';
        $requete .= ' WHERE ';
        $requete .= '  id_parent = ' . (int) $id_site_rubrique;
        $requete .= ' ORDER BY date ASC';
        $elements = $this->bdd->obtenirTous($requete);

        $rubriques = [];
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $rubrique = new Rubrique();
                $rubrique->remplir($element);
                $rubriques[] = $rubrique;
            }
        }

        return $rubriques;
    }

    public function obtenirListe(string $champs = '*',
                          string $ordre = 'titre',
                          $filtre = null,
                          $associatif = false,
    ) {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_site_rubrique ';

        if ($filtre && trim((string) $filtre) !== '') {
            $requete .= sprintf(' WHERE afup_site_rubrique.nom LIKE %s ', $this->bdd->echapper('%' . $filtre . '%'));
        }

        $requete .= 'ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->bdd->obtenirAssociatif($requete);
        } else {
            return $this->bdd->obtenirTous($requete);
        }
    }
}
