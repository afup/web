<?php
namespace Afup\Site\Corporate;

use Afup\Site\Pagination;
use Afup\Site\Utils\Configuration;

class Rubrique
{
    const ID_RUBRIQUE_ACTUALITES = 9;
    const ID_RUBRIQUE_ASSOCIATION = 85;
    const ID_RUBRIQUE_ANTENNES = 84;
    const ID_RUBRIQUE_INFORMATIONS_PRATIQUES = 86;
    const ID_RUBRIQUE_NOS_ACTIONS = 88;

    public $id;
    public $id_personne_physique;
    public $id_parent;
    public $nom;
    public $raccourci;
    public $descriptif;
    public $contenu;
    public $position;
    public $icone;
    public $date;
    public $etat;
    public $pagination;
    public $feuille_associee;

    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    protected $bdd;

    /**
     * @var Configuration
     */
    protected $conf;
    protected $page_courante;

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

        if (isset($_GET['page_courante'])) {
            $this->page_courante = (int)$_GET['page_courante'];
        }
        if ($this->page_courante <= 0) {
            $this->page_courante = 1;
        }
    }

    function afficher()
    {
        $article = new Article(null, $this->bdd);
        $article->id_site_rubrique = $this->id;
        $article->charger_dernier_depuis_rubrique();

        $html_pagination = $this->pagination_html();

        return
        '<h1>' . $this->titre() . '</h1>' .
        $this->corps() .
        $html_pagination .
        $this->rubriques_dans_la_rubrique() .
        $this->articles_dans_la_rubrique() .
        $html_pagination .
        '<div class="breadcrumbs">' . $this->fil_d_ariane() . '</div>';
    }

    function rubrique()
    {
        return '<ul id="Header">' .
        '<li id="HeaderImg">' .
        $this->image_sous_navigation() .
        '</li>' .
        '<li id="HeaderTitle">' .
        $this->titre() .
        '</li>' .
        '</ul>';
    }

    function image_sous_navigation()
    {
        return '<img src="' . Site::WEB_PATH . 'templates/site/images/' . $this->icone . '" />';
    }

    function titre()
    {
        return $this->nom;
    }

    function liste_sous_navigation()
    {
        $liste = "";

        $sous_rubriques = $this->sous_rubriques();
        if (count($sous_rubriques) > 0) {
            $liste .= '<ul class="Txt NavCategories">';
            foreach ($sous_rubriques as $rubrique) {
                $liste .= '<li><a href="' . $rubrique->route() . '">' . $rubrique->nom . '</a></li>';
            }
            $liste .= '</ul>';
        }

        $autres_articles = $this->autres_articles();
        if (count($autres_articles) > 0) {
            $liste .= '<ul class="Txt">';
            foreach ($autres_articles as $article) {
                $liste .= '<li><a href="' . $article->route() . '">' . $article->titre . '</a></li>';
            }
            $liste .= '</ul>';
        }

        return $liste;
    }

    function remplir($rubrique)
    {
        $this->id = $rubrique['id'];
        $this->id_parent = $rubrique['id_parent'];
        $this->id_personne_physique = $rubrique['id_personne_physique'];
        $this->nom = $rubrique['nom'];
        $this->raccourci = $rubrique['raccourci'];
        $this->descriptif = $rubrique['descriptif'];
        $this->contenu = $rubrique['contenu'];
        $this->icone = $rubrique['icone'];
        $this->date = $rubrique['date'];
        $this->etat = $rubrique['etat'];
        $this->pagination = $rubrique['pagination'] ?? null;
        $this->feuille_associee = $rubrique['feuille_associee'];
    }

    function charger()
    {
        $requete = 'SELECT *
                    FROM afup_site_rubrique
                    WHERE id = ' . $this->bdd->echapper($this->id);
        $rubrique = $this->bdd->obtenirEnregistrement($requete);
        $this->remplir($rubrique);
    }

    function date()
    {
        return date("d/m/Y", $this->date);
    }

    function annexe()
    {
        if ($this->etat <= 0) {
            return false;
        }

        $annexe = '';

        return $annexe;
    }

    function corps()
    {
        if ($this->etat <= 0) {
            return false;
        }

        $corps = "";
        if (!empty($this->contenu)) {
            $corps .= '<div class="contenu">' . $this->contenu . '</div>';
        }

        return $corps;
    }

    function exportable()
    {
        return [
            'id' => $this->id,
            'id_parent' => $this->id_parent,
            'id_personne_physique' => $this->id_personne_physique,
            'nom' => $this->nom,
            'raccourci' => $this->raccourci,
            'descriptif' => $this->descriptif,
            'contenu' => $this->contenu,
            'date' => date('Y-m-d', $this->date),
            'etat' => $this->etat,
            'feuille_associee' => $this->feuille_associee,
        ];
    }

    function positionable()
    {
        $positions = [];
        for ($i = 9; $i >= -9; $i--) {
            $positions[$i] = $i;
        }

        return $positions;
    }

    function supprimer()
    {
        $requete = 'DELETE FROM afup_site_rubrique WHERE id = ' . $this->bdd->echapper($this->id);
        return $this->bdd->executer($requete);
    }

    function modifier()
    {
        $requete = 'UPDATE afup_site_rubrique
        			SET
        			id_parent            = ' . $this->bdd->echapper($this->id_parent) . ',
        			id_personne_physique = ' . $this->bdd->echapper($this->id_personne_physique) . ',
        			position             = ' . $this->bdd->echapper($this->position) . ',
        			date                 = ' . $this->bdd->echapper($this->date) . ',
        			nom                  = ' . $this->bdd->echapper($this->nom) . ',
        			raccourci            = ' . $this->bdd->echapper($this->raccourci) . ',
         			descriptif           = ' . $this->bdd->echapper($this->descriptif) . ',
           			contenu              = ' . $this->bdd->echapper($this->contenu) . ',
           			icone                = ' . $this->bdd->echapper($this->icone) . ',
           			etat                 = ' . $this->bdd->echapper($this->etat) . ',
           			feuille_associee     = ' . $this->bdd->echapper($this->feuille_associee) . '
           			WHERE id             = ' . $this->bdd->echapper($this->id);

        return $this->bdd->executer($requete);
    }

    function inserer()
    {
        $requete = 'INSERT INTO afup_site_rubrique
        			SET
        			id_parent            = ' . $this->bdd->echapper($this->id_parent) . ',
        			id_personne_physique = ' . $this->bdd->echapper($this->id_personne_physique) . ',
        			position             = ' . $this->bdd->echapper($this->position) . ',
        			date                 = ' . $this->bdd->echapper($this->date) . ',
        			nom                  = ' . $this->bdd->echapper($this->nom) . ',
        			raccourci            = ' . $this->bdd->echapper($this->raccourci) . ',
         			descriptif           = ' . $this->bdd->echapper($this->descriptif) . ',
           			contenu              = ' . $this->bdd->echapper($this->contenu) . ',
           			icone                = ' . $this->bdd->echapper($this->icone) . ',
           			feuille_associee     = ' . $this->bdd->echapper($this->feuille_associee) . ',
           			etat                 = ' . $this->bdd->echapper($this->etat);
        if ($this->id > 0) {
            $requete .= ', id            = ' . $this->bdd->echapper($this->id);
        }

        $resultat = $this->bdd->executer($requete);
        if ($resultat) {
            $this->id = $this->bdd->obtenirDernierId();
        }

        return $resultat;
    }

    function route()
    {
        return Site::WEB_PATH.Site::WEB_PREFIX.Site::WEB_QUERY_PREFIX . $this->raccourci . '/' . $this->id;
    }

    function nom()
    {
        return $this->nom;
    }

    function fil_d_ariane()
    {
        $fil = '/ <a href="' . $this->route() . '">' . $this->nom . '</a>';

        if ($this->id_parent > 0) {
            $id_parent = $this->id_parent;
            while ($id_parent > 0) {
                $parent = new Rubrique($id_parent, $this->bdd, $this->conf);
                $parent->charger();
                $fil = '/ <a href="' . $parent->route() . '">' . $parent->nom . '</a> ' . $fil;
                $id_parent = $parent->id_parent;
            }
        }

        return $fil;
    }

    function sous_rubriques()
    {
        $rubriques = new Rubriques();
        return $rubriques->chargerSousRubriques($this->id);
    }

    function rubriques_dans_la_rubrique()
    {
        $sous_rubriques = $this->sous_rubriques();
        $liste = "";
        if (count($sous_rubriques) > 0) {
            $liste = '<ul class="Txt Rubriques">';
            foreach ($sous_rubriques as $rubrique) {
                $liste .= '<li><a href="' . $rubrique->route() . '">' . $rubrique->nom . '</a></li>';
            }
            $liste .= '</ul>';
        }

        return $liste;
    }

    function articles_dans_la_rubrique()
    {
        $autres_articles = $this->autres_articles();
        $articles = "";

        if (count($autres_articles) > 0) {
            foreach ($autres_articles as $article) {
                $articles .= '<a class="article article-teaser" href="' . $article->route() . '">' .
                    '<time datetime="' . date("Y-m-d", $article->date) . '">' . $article->date() . '</time>' .
                    '<h2>' . $article->titre . '</h2>' .
                    '<p>' . $article->teaser() . '</p>' .
                    '</a>';
            }
        }

        return $articles;
    }

    function autres_articles()
    {
        $autres = [];

        $requete = ' SELECT';
        $requete .= '  * ';
        $requete .= ' FROM';
        $requete .= '  afup_site_article ';
        $requete .= ' WHERE ';
        $requete .= '  etat = 1 ';
        $requete .= ' AND id_site_rubrique = ' . (int)$this->id;
        $requete .= ' ORDER BY date DESC';

        if ($this->pagination > 0) {
            $offset = ($this->page_courante - 1) * $this->pagination;
            $limit = $this->pagination;
            $requete .= ' LIMIT ' . (int)$offset . ', ' . $limit;
        }

        $articles = $this->bdd->obtenirTous($requete);

        if (is_array($articles)) {
            foreach ($articles as $article) {
                $autre = new Article($article['id'], $this->bdd);
                $autre->remplir($article);
                $autres[] = $autre;
            }
        }

        return $autres;
    }

    function compte_autres_articles()
    {
        $requete = ' SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= ' FROM';
        $requete .= '  afup_site_article ';
        $requete .= ' WHERE ';
        $requete .= '  etat = 1 ';
        $requete .= ' AND id_site_rubrique = ' . (int)$this->id;
        $requete .= ' ORDER BY date DESC';

        return $this->bdd->obtenirUn($requete);
    }

    function pagination_html()
    {
        if ($this->pagination) {
            return new Pagination(
                $this->page_courante,
                $this->pagination,
                $this->compte_autres_articles(),
                [$this, 'genere_route']
            );
        } else {
            return '';
        }
    }

    function genere_route($params)
    {
        $page = $params['page'] ?? 1;
        $url = $this->route();
        if ($page != 1) {
            if (strpos($url, '?') === false) {
                $url .= '?';
            } else {
                $url .= '&';
            }
            $url .= 'page_courante=' . $page;
        }

        return $url;
    }
}
