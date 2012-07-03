<?php
require_once dirname(__FILE__).'/AFUP_Site.php';

class AFUP_Site_Page_Lille extends AFUP_Site_Page {
    function header() {
        $header = new AFUP_Site_Header_Lille();
        $header->setTitle($this->title);
        return $header->render();
    }

    function footer() {
        $footer = new AFUP_Site_Footer_Lille();
        return $footer->render();
    }

    function menu() {
        $branche = new AFUP_Site_Branche($this->bdd);
        
        return '<div id="header-menu">'.
			'Pour en savoir plus sur l\'antenne de lille'.
			$branche->naviguer(36, 2, "header-menu-local", "pages/lille/").
			'Et pendant ce temps-là, l\'association national...'.
			$branche->naviguer(5, 2, "header-menu-local").
			'</div>';
    }

    function definirRoute($route) {
        $this->route = $route;
        switch (true) {
            case preg_match("%\s*/[0-9]*/\s*%", $this->route):
                list(, $id, ) = explode("/", $this->route);
                $article = new AFUP_Site_Article_Lille($id, $this->bdd);
                $article->charger();
                $this->title = $article->titre;
                $this->content = $article->afficher();
                break;

            case preg_match("%s*/[0-9]*%", $this->route):
                list(, $id) = explode("/", $this->route);
                $rubrique = new AFUP_Site_Rubrique_Lille($id, $this->bdd);
                $rubrique->charger();
                $this->title = $rubrique->nom;
                $this->content = $rubrique->afficher();
                break;
        }
    }

    function content() {
        return $this->content;
    }
}

class AFUP_Site_Header_Lille extends AFUP_Site_Header {
    function __construct() {
        $this->setTitle('Lille');
        $this->addCSS('templates/lille/medias/css/lille.css');
        $this->javascript = '';
        $this->addRSS();
    }

    function setTitle($string) {
        $this->title = '<title>'.$string.' | Antenne local de l\'AFUP : Lille</title>';
    }

    function addRSS() {
        $conf = $GLOBALS['AFUP_CONF'];
        $rssFile = $conf->obtenir('web|path').'/pages/lille/rss.php';
        $this->rss = '<link rel="alternate" type="application/rss+xml" href="' . $rssFile .'" title="Derni&egraves actus de l\'AFUP Lille"/>';
    }
}

class AFUP_Site_Footer_Lille extends AFUP_Site_Footer {
}

class AFUP_Site_Article_Lille extends AFUP_Site_Article {
	function afficher() {
		return '<div id="article-ariane">'.$this->fil_d_ariane("pages/lille/")."</div>".
				'<h1>'.$this->titre().'</h1>'.
				$this->corps().
				'<div class="article-date">' . $this->date() .'</div>';
	}
}

class AFUP_Site_Rubrique_Lille extends AFUP_Site_Rubrique {
	function afficher() {
		$articles = new AFUP_Site_Articles($this->bdd);
		$derniers_articles = $articles->chargerArticlesDeRubrique($this->id);

		$articles = array();
		foreach ($derniers_articles as $article) {
			$descriptif = ($article->descriptif) ? $article->descriptif : $article->chapeau;
			$articles[] = '<h2><a href="'.$article->route("pages/lille/").'">'.$article->titre.'</a></h2>
			<p>'.$descriptif.'</p><p class="rubrique-article-date">'.date('d/m/Y', $article->date).'</p>';
		}
		
		return '<h1>'.$this->nom.'</h1>
				<ul class="rubrique-articles"><li>'.join($articles, '</li><li>').'</li></ul>';
	}
}