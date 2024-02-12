<?php
namespace Afup\Site\Corporate;

class Branche
{
    public $navigation = 'nom';

    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    protected $bdd;

    /**
     * @var mixed
     */
    private $conf;

    function __construct($bdd = false, $conf = false)
    {
        if ($bdd) {
            $this->bdd = $bdd;
        } else {
            $this->bdd = new _Site_Base_De_Donnees();
        }
        if ($conf) {
            $this->conf = $conf;
        } else {
            $this->conf = $GLOBALS['AFUP_CONF'];
        }
    }

    function navigation_avec_image($bool = false)
    {
        if ($bool) {
            $this->navigation = 'image';
        }
    }

    public function feuillesEnfants($id)
    {
        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id_parent = '.$this->bdd->echapper($id).'
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

    function naviguer($id, $profondeur = 1, $identification = "")
    {
        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id = ' . $this->bdd->echapper($id) . '
                    AND etat = 1';
        $racine = $this->bdd->obtenirEnregistrement($requete);

        $feuilles = $this->extraireFeuilles($id, $profondeur);
        if ($feuilles) {
            $navigation = '<ul id="' . $identification . '" class="' . Site::raccourcir($racine['nom']) . '">' . $feuilles . '</ul>';
        } else {
            $navigation = '';
        }

        return $navigation;
    }

    function extraireFeuilles($id, $profondeur) {
        $extraction = '';

        $requete = 'SELECT *
                    FROM afup_site_feuille
                    WHERE id_parent = '.$this->bdd->echapper($id).'
                    AND etat = 1
                    ORDER BY position';
        $feuilles = $this->bdd->obtenirTous($requete);

        if (is_array($feuilles)) {
            foreach ($feuilles as $feuille) {
                $class = "";
                if ($extraction === "") {
                    $class = ' class="top"';
                }
                switch (true) {
                    case preg_match('#^http://#', $feuille['lien']):
                    case preg_match('#^/#', $feuille['lien']):
                        $route = $feuille['lien'];
                        break;
                    default:
                        $route = Site::WEB_PATH.Site::WEB_PREFIX.Site::WEB_QUERY_PREFIX.$feuille['lien'];
                        break;
                }
                $extraction .= '<li'.$class.'>';
                if ($this->navigation == 'image' && $feuille['image'] !== null) {
                    $extraction .= '<a href="'.$route.'"><img alt="'.$feuille['alt'].'" src="'.Site::WEB_PATH.'templates/site/images/'.$feuille['image'].'" /><br>';
                    $extraction .= $feuille['nom'] . '</a><br>';
                    $extraction .= $feuille['alt'];
                } else {
                    $extraction .= '<a href="'.$route.'">' . $feuille['nom'] . '</a>';
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
