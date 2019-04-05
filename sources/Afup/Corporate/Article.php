<?php
namespace Afup\Site\Corporate;


use Afup\Site\Utils\Configuration;

class Article
{
    public $id;
    public $id_site_rubrique;
    public $id_personne_physique;
    public $surtitre;
    public $titre;
    public $raccourci;
    public $descriptif;
    public $chapeau;
    public $contenu;
    public $type_contenu;
    public $position;
    public $date;
    public $etat;
    public $route;

    public $theme;
    public $id_forum;

    const THEME_ID_CYCLE_CONFERENCE = 1;
    const THEME_ID_ANTENNES = 2;
    const THEME_ID_ASSOCIATIF = 3;
    const THEME_ID_BAROMETRE = 4;
    const THEME_ID_AFUP_SOUTIEN = 5;

    const TYPE_CONTENU_HTML = 'html';
    const TYPE_CONTENU_MARKDOWN = 'markdown';

    public static function getThemesLabels()
    {
        return [
            self::THEME_ID_CYCLE_CONFERENCE => 'Cycles de conférences',
            self::THEME_ID_ANTENNES => 'Antennes',
            self::THEME_ID_ASSOCIATIF => 'Associatif',
            self::THEME_ID_BAROMETRE => 'Baromètre',
            self::THEME_ID_AFUP_SOUTIEN => "L'AFUP soutient",
        ];
    }

    public static function getThemeLabel($code)
    {
        $themes = self::getThemesLabels();

        if (isset($themes[$code])) {
            return $themes[$code];
        }

        return '';
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    protected $rubrique;

    /**
     * @var Configuration
     */
    protected $conf;
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    protected $bdd;

    function __construct($id = 0, $bdd = false, $conf = false)
    {
        $this->id = $id;

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

    function afficher() {
        return '
            <article>' .
        '<time datetime=' . date("Y-m-d", $this->date) . '>' . $this->date() . '</time>' .
        '<h1>' . $this->titre() . '</h1>' .
        $this->corps() .
        '<div class="breadcrumbs">' . $this->fil_d_ariane() . '</div>' .
        '</article>';
    }

    function titre()
    {
        return $this->titre;
    }

    public function getCode()
    {
        return $this->id . '-' . $this->raccourci;
    }

    function teaser()
    {
        switch (true) {
            case !empty($this->chapeau):
                $teaser = $this->chapeau;
                break;
            case !empty($this->descriptif):
                $teaser = $this->descriptif;
                break;
            default:
                $teaser = substr(strip_tags($this->contenu), 0, 200);
        }
        return $teaser;
    }

    function corps()
    {
        if ($this->etat <= 0) {
            return false;
        }

        $corps = "";
        if (!empty($this->surtitre)) {
            $corps .= '<p class="surtitre">' . $this->surtitre . '</p>';
        }
        if (!empty($this->soustitre)) {
            $corps .= '<h2>' . $this->soustitre . '</h2>';
        }
        if (!empty($this->chapeau)) {
            $corps .= '<blockquote>' . $this->chapeau . '</blockquote>';
        } else {
            $corps .= '<blockquote>' . $this->descriptif . '</blockquote>';
        }
        if (!empty($this->contenu)) {
            $corps .= $this->contenu;
        }

        return $corps;
    }

    function date()
    {
        return date("d/m/y", $this->date);
    }

    function positionable()
    {
        $positions = array();
        for ($i = 9; $i >= -9; $i--) {
            $positions[$i] = $i;
        }

        return $positions;
    }

    function exportable()
    {
        return array(
            'id' => $this->id,
            'id_site_rubrique' => $this->id_site_rubrique,
            'id_personne_physique' => $this->id_personne_physique,
            'surtitre' => $this->surtitre,
            'titre' => $this->titre,
            'raccourci' => $this->raccourci,
            'descriptif' => $this->descriptif,
            'chapeau' => $this->chapeau,
            'contenu' => $this->contenu,
            'type_contenu' => $this->type_contenu,
            'position' => $this->position,
            'date' => date('Y-m-d', $this->date),
            'theme' => $this->theme,
            'id_forum' => $this->id_forum,
            'etat' => $this->etat,
        );
    }

    function supprimer()
    {
        $requete = 'DELETE FROM afup_site_article WHERE id = ' . $this->bdd->echapper($this->id);
        return $this->bdd->executer($requete);
    }

    function charger()
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE id = ' . $this->bdd->echapper($this->id);
        $this->remplir($this->bdd->obtenirEnregistrement($requete));
    }

    function chargerDepuisRaccourci($raccourci)
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE CONCAT(id, "-", raccourci) = ' . $this->bdd->echapper($raccourci);
        $this->remplir($this->bdd->obtenirEnregistrement($requete));
    }

    function charger_dernier_depuis_rubrique()
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE id_site_rubrique = ' . $this->bdd->echapper($this->id_site_rubrique) .
            ' ORDER BY date DESC LIMIT 1';
        $this->remplir($this->bdd->obtenirEnregistrement($requete));
    }

    function remplir($article)
    {
        $this->id = $article['id'];
        $this->id_site_rubrique = $article['id_site_rubrique'];
        $this->id_personne_physique = $article['id_personne_physique'];
        $this->surtitre = $article['surtitre'];
        $this->titre = $article['titre'];
        $this->raccourci = $article['raccourci'];
        $this->descriptif = $article['descriptif'];
        $this->chapeau = $article['chapeau'];
        $this->contenu = $article['contenu'];
        $this->type_contenu = $article['type_contenu'];
        $this->position = $article['position'];
        $this->date = $article['date'];
        $this->etat = $article['etat'];
        $this->theme = $article['theme'];
        $this->id_forum = $article['id_forum'];
        $this->route = $this->route();
    }

    function modifier()
    {
        $requete = 'UPDATE afup_site_article
        			SET
        			id_site_rubrique      = ' . $this->bdd->echapper($this->id_site_rubrique) . ',
        			id_personne_physique  = ' . $this->bdd->echapper($this->id_personne_physique) . ',
        			surtitre              = ' . $this->bdd->echapper($this->surtitre) . ',
        			titre                 = ' . $this->bdd->echapper($this->titre) . ',
        			raccourci             = ' . $this->bdd->echapper($this->raccourci) . ',
        			descriptif            = ' . $this->bdd->echapper($this->descriptif) . ',
        			chapeau               = ' . $this->bdd->echapper($this->chapeau) . ',
        			contenu               = ' . $this->bdd->echapper($this->contenu) . ',
        			type_contenu               = ' . $this->bdd->echapper($this->type_contenu) . ',
        			position              = ' . $this->bdd->echapper($this->position) . ',
        			date                  = ' . $this->bdd->echapper($this->date) . ',
        			theme                 = ' . $this->bdd->echapper($this->theme) . ',
        			id_forum              = ' . $this->bdd->echapper($this->id_forum) . ',
        			etat                  = ' . $this->bdd->echapper($this->etat) . '
                    WHERE
                    id = ' . $this->bdd->echapper($this->id);

        return $this->bdd->executer($requete);
    }

    function inserer()
    {
        $requete = 'INSERT INTO afup_site_article
        			SET
        			id_site_rubrique      = ' . $this->bdd->echapper($this->id_site_rubrique) . ',
        			id_personne_physique  = ' . $this->bdd->echapper($this->id_personne_physique) . ',
        			surtitre              = ' . $this->bdd->echapper($this->surtitre) . ',
        			titre                 = ' . $this->bdd->echapper($this->titre) . ',
        			raccourci             = ' . $this->bdd->echapper($this->raccourci) . ',
        			descriptif            = ' . $this->bdd->echapper($this->descriptif) . ',
        			chapeau               = ' . $this->bdd->echapper($this->chapeau) . ',
        			contenu               = ' . $this->bdd->echapper($this->contenu) . ',
        			type_contenu               = ' . $this->bdd->echapper($this->type_contenu) . ',
        			position              = ' . $this->bdd->echapper($this->position) . ',
        			date                  = ' . $this->bdd->echapper($this->date) . ',
        			theme                 = ' . $this->bdd->echapper($this->theme) . ',
        			id_forum              = ' . $this->bdd->echapper($this->id_forum) . ',
        			etat                  = ' . $this->bdd->echapper($this->etat);
        if ($this->id > 0) {
            $requete .= ', id = ' . $this->bdd->echapper($this->id);
        }

        $resultat = $this->bdd->executer($requete);

        if ($resultat) {
            $this->id = $this->bdd->obtenirDernierId();
        }

        return $resultat;
    }

    function route()
    {
        $rubrique = new Rubrique($this->id_site_rubrique, $this->bdd, $this->conf);
        $rubrique->charger();
        if (empty($rubrique->raccourci)) {
            $rubrique->raccourci = 'rubrique';
        }

        return $this->conf->obtenir('web|path') . $this->conf->obtenir('site|prefix') . $this->conf->obtenir('site|query_prefix') . $rubrique->raccourci . '/' . $this->id . '/' . $this->raccourci;
    }

    function fil_d_ariane()
    {
        $fil = '';

        if ($this->id_site_rubrique > 0) {
            $rubrique = new Rubrique($this->id_site_rubrique, $this->bdd, $this->conf);
            $rubrique->charger();
            $fil = $rubrique->fil_d_ariane() . $fil;
        }

        return $fil;
    }

    function autres_articles()
    {
        $articles = new Articles($this->bdd);

        return $articles->chargerArticlesDeRubrique($this->id_site_rubrique);
    }

    public function isTypeContenuMarkdown()
    {
        return self::TYPE_CONTENU_MARKDOWN == $this->type_contenu;
    }

    public function getChapeau()
    {
        $chapeau = $this->chapeau;

        if ($this->isTypeContenuMarkdown()) {
            $parseDown = new \Parsedown();
            $chapeau = $parseDown->parse($chapeau);
        }

        return $chapeau;
    }
}
