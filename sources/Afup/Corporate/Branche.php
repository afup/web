<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Branche
{
    public $navigation = 'nom';

    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public function navigation_avec_image($bool = false): void
    {
        if ($bool) {
            $this->navigation = 'image';
        }
    }

    public function feuillesEnfants($id)
    {
        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id_parent = ' . $this->bdd->echapper($id) . '
                    AND etat = 1
                    ORDER BY position';
        return $this->bdd->obtenirTous($requete);
    }

    public function getNom($id)
    {
        $requete = 'SELECT nom
                    FROM afup_site_feuille
                    WHERE id = ' . $this->bdd->echapper($id) . '
                    AND etat = 1';
        $enregistrement = $this->bdd->obtenirEnregistrement($requete);

        if (false === $enregistrement) {
            return null;
        }

        return $enregistrement['nom'];
    }

    public function naviguer($id, $profondeur = 1, string $identification = ""): string
    {
        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id = ' . $this->bdd->echapper($id) . '
                    AND etat = 1';
        $racine = $this->bdd->obtenirEnregistrement($requete);

        if ($racine === false) {
            return '';
        }

        $feuilles = $this->extraireFeuilles($id, $profondeur);
        if ($feuilles !== '' && $feuilles !== '0') {
            $navigation = '<ul id="' . $identification . '" class="' . Site::raccourcir($racine['nom']) . '">' . $feuilles . '</ul>';
        } else {
            $navigation = '';
        }

        return $navigation;
    }

    public function extraireFeuilles($id, $profondeur): string
    {
        $extraction = '';

        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id_parent = ' . $this->bdd->echapper($id) . '
                    AND etat = 1
                    ORDER BY position';
        $feuilles = $this->bdd->obtenirTous($requete);

        if (is_array($feuilles)) {
            foreach ($feuilles as $feuille) {
                $class = "";
                if ($extraction === "") {
                    $class = ' class="top"';
                }
                $route = match (true) {
                    preg_match('#^http://#', (string) $feuille['lien']), preg_match('#^/#', (string) $feuille['lien']) => $feuille['lien'],
                    default => Site::WEB_PATH . Site::WEB_PREFIX . Site::WEB_QUERY_PREFIX . $feuille['lien'],
                };
                $extraction .= '<li' . $class . '>';
                if ($this->navigation == 'image' && $feuille['image'] !== null) {
                    $extraction .= '<a href="' . $route . '"><img alt="' . $feuille['alt'] . '" src="' . Site::WEB_PATH . 'templates/site/images/' . $feuille['image'] . '" /><br>';
                    $extraction .= $feuille['nom'] . '</a><br>';
                    $extraction .= $feuille['alt'];
                } else {
                    $extraction .= '<a href="' . $route . '">' . $feuille['nom'] . '</a>';
                }
                $extraction .= '</li>';
                if ($profondeur > 0) {
                    $extraction .= $this->naviguer($feuille['id'], $profondeur - 1);
                }
            }
        }

        return $extraction;
    }
}
