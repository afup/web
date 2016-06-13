<?php
namespace Afup\Site\Corporate\Lille;

use Afup\Site\Corporate\Rubrique;
use Afup\Site\Corporate\Articles;



class _Rubrique_Lille extends Rubrique
{
    function afficher()
    {
        $articles = new Articles($this->bdd);
        $derniers_articles = $articles->chargerArticlesDeRubrique($this->id);

        $articles = array();
        foreach ($derniers_articles as $article) {
            $descriptif = ($article->descriptif) ? $article->descriptif : $article->chapeau;
            $articles[] = '<h2><a href="' . $article->route("pages/lille/") . '">' . $article->titre . '</a></h2>
			<p>' . $descriptif . '</p><p class="rubrique-article-date">' . date('d/m/Y', $article->date) . '</p>';
        }

        return '<h1>' . $this->nom . '</h1>
				<ul class="rubrique-articles"><li>' . join($articles, '</li><li>') . '</li></ul>';
    }
}