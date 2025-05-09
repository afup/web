<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Configuration;

class Article
{
    public $soustitre;
    public $id_site_rubrique;
    public $id_personne_physique;
    public $titre;
    public $raccourci;
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

    public static function getThemesLabels(): array
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

        return $themes[$code] ?? '';
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    protected $rubrique;

    /**
     * @var Configuration
     */
    protected $conf;
    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct(
        public $id = 0,
        $bdd = false,
        $conf = false,
    ) {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();

        $this->conf = $conf ?: $GLOBALS['AFUP_CONF'];
    }

    public function afficher(): string
    {
        return '
            <article>' .
        '<time datetime=' . date("Y-m-d", $this->date) . '>' . $this->date() . '</time>' .
        '<h1>' . $this->titre() . '</h1>' .
        $this->corps() .
        '<div class="breadcrumbs">' . $this->fil_d_ariane() . '</div>' .
        '</article>';
    }

    public function titre()
    {
        return $this->titre;
    }

    public function getCode(): string
    {
        return $this->id . '-' . $this->raccourci;
    }

    public function teaser()
    {
        $teaser = match (true) {
            !empty($this->chapeau) => $this->chapeau,
            default => substr(strip_tags((string) $this->contenu), 0, 200),
        };
        return $teaser;
    }

    public function corps()
    {
        if ($this->etat <= 0) {
            return false;
        }

        $corps = "";
        if (!empty($this->soustitre)) {
            $corps .= '<h2>' . $this->soustitre . '</h2>';
        }
        if (!empty($this->chapeau)) {
            $corps .= '<blockquote>' . $this->chapeau . '</blockquote>';
        }

        if (!empty($this->contenu)) {
            $corps .= $this->contenu;
        }

        return $corps;
    }

    public function date(): string
    {
        return date("d/m/y", $this->date);
    }

    /**
     * @return int[]
     */
    public function positionable(): array
    {
        $positions = [];
        for ($i = 9; $i >= -9; $i--) {
            $positions[$i] = $i;
        }

        return $positions;
    }

    public function exportable(): array
    {
        return [
            'id' => $this->id,
            'id_site_rubrique' => $this->id_site_rubrique,
            'id_personne_physique' => $this->id_personne_physique,
            'titre' => $this->titre,
            'raccourci' => $this->raccourci,
            'chapeau' => $this->chapeau,
            'contenu' => $this->contenu,
            'type_contenu' => $this->type_contenu,
            'position' => $this->position,
            'date' => date('Y-m-d H:i:s', (int) $this->date),
            'theme' => $this->theme,
            'id_forum' => $this->id_forum,
            'etat' => $this->etat,
        ];
    }

    public function supprimer()
    {
        $requete = 'DELETE FROM afup_site_article WHERE id = ' . $this->bdd->echapper($this->id);
        return $this->bdd->executer($requete);
    }

    public function charger(): void
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE id = ' . $this->bdd->echapper($this->id);
        $data = $this->bdd->obtenirEnregistrement($requete);
        if ($data) {
            $this->remplir($data);
        }
    }

    public function chargerDepuisRaccourci($raccourci): void
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE CONCAT(id, "-", raccourci) = ' . $this->bdd->echapper($raccourci);
        $data = $this->bdd->obtenirEnregistrement($requete);
        if ($data) {
            $this->remplir($data);
        }
    }

    public function charger_dernier_depuis_rubrique(): void
    {
        $requete = 'SELECT *
                    FROM afup_site_article
                    WHERE id_site_rubrique = ' . $this->bdd->echapper($this->id_site_rubrique) .
            ' ORDER BY date DESC LIMIT 1';
        $data = $this->bdd->obtenirEnregistrement($requete);
        if ($data) {
            $this->remplir($data);
        }
    }

    public function remplir(array $article): void
    {
        $this->id = $article['id'];
        $this->id_site_rubrique = $article['id_site_rubrique'];
        $this->id_personne_physique = $article['id_personne_physique'];
        $this->titre = $article['titre'];
        $this->raccourci = $article['raccourci'];
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

    public function modifier()
    {
        $requete = 'UPDATE afup_site_article
        			SET
        			id_site_rubrique      = ' . $this->bdd->echapper($this->id_site_rubrique) . ',
        			id_personne_physique  = ' . $this->bdd->echapper($this->id_personne_physique ?: null) . ',
        			titre                 = ' . $this->bdd->echapper($this->titre) . ',
        			raccourci             = ' . $this->bdd->echapper($this->raccourci) . ',
        			chapeau               = ' . $this->bdd->echapper($this->chapeau) . ',
        			contenu               = ' . $this->bdd->echapper($this->contenu) . ',
        			type_contenu               = ' . $this->bdd->echapper($this->type_contenu) . ',
        			position              = ' . $this->bdd->echapper($this->position) . ',
        			date                  = ' . $this->bdd->echapper($this->date) . ',
        			theme                 = ' . $this->bdd->echapper($this->theme ?: null) . ',
        			id_forum              = ' . $this->bdd->echapper($this->id_forum ?: null) . ',
        			etat                  = ' . $this->bdd->echapper($this->etat) . '
                    WHERE
                    id = ' . $this->bdd->echapper($this->id);

        return $this->bdd->executer($requete);
    }

    public function inserer()
    {
        $requete = 'INSERT INTO afup_site_article
        			SET
        			id_site_rubrique      = ' . $this->bdd->echapper($this->id_site_rubrique) . ',
        			id_personne_physique  = ' . $this->bdd->echapper($this->id_personne_physique ?: null) . ',
        			titre                 = ' . $this->bdd->echapper($this->titre) . ',
        			raccourci             = ' . $this->bdd->echapper($this->raccourci) . ',
        			chapeau               = ' . $this->bdd->echapper($this->chapeau) . ',
        			contenu               = ' . $this->bdd->echapper($this->contenu) . ',
        			type_contenu               = ' . $this->bdd->echapper($this->type_contenu) . ',
        			position              = ' . $this->bdd->echapper($this->position) . ',
        			date                  = ' . $this->bdd->echapper($this->date) . ',
        			theme                 = ' . $this->bdd->echapper($this->theme ?: null) . ',
        			id_forum              = ' . $this->bdd->echapper($this->id_forum ?: null) . ',
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

    public function route(): string
    {
        $rubrique = new Rubrique($this->id_site_rubrique, $this->bdd, $this->conf);
        $rubrique->charger();
        if (empty($rubrique->raccourci)) {
            $rubrique->raccourci = 'rubrique';
        }

        return Site::WEB_PATH . Site::WEB_PREFIX . Site::WEB_QUERY_PREFIX . $rubrique->raccourci . '/' . $this->id . '/' . $this->raccourci;
    }

    public function fil_d_ariane(): string
    {
        $fil = '';

        if ($this->id_site_rubrique > 0) {
            $rubrique = new Rubrique($this->id_site_rubrique, $this->bdd, $this->conf);
            $rubrique->charger();
            $fil = $rubrique->fil_d_ariane() . $fil;
        }

        return $fil;
    }

    public function autres_articles(): array
    {
        $articles = new Articles($this->bdd);

        return $articles->chargerArticlesDeRubrique($this->id_site_rubrique);
    }

    public function isTypeContenuMarkdown(): bool
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
