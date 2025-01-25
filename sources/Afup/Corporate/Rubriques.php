<?php
namespace Afup\Site\Corporate;

class Rubriques
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

    function chargerSousRubriques($id_site_rubrique)
    {
        $requete = ' SELECT';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_site_rubrique ';
        $requete .= ' WHERE ';
        $requete .= '  id_parent = ' . (int)$id_site_rubrique;
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

    function obtenirListe($champs = '*',
                          $ordre = 'titre',
                          $filtre = null,
                          $associatif = false
    ) {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_site_rubrique ';

        if (strlen(trim($filtre)) > 0) {
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
