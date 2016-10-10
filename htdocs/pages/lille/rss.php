<?php
use Afup\Site\Corporate\Articles;

require_once dirname(__FILE__).'/../../../sources/Afup/Bootstrap/Http.php';


$articles         = new Articles($bdd);
$derniersArticles = array();

foreach($articles->chargerArticlesDeRubrique(73, 20) as $article) {
	$derniersArticles[] = array(
    	'titre'   => $article->titre,
    	'contenu' => $article->contenu,
    	'url'     => $article->route ,
    	'maj'     => date(DATE_RSS,$article->date),
    );
}

$feed = array(
	'title'         => "Le flux RSS de l'AFUP",
	'url'           => 'http://afup.org/',
	'link'          => 'http://afup.org/rss.php',
	'email'         => 'bonjour@afup.org',
	'author'        => 'Nicolas Silberman / AFUP',
	'date'          => date(DATE_RSS),
	'lastBuildDate' => 
        isset($derniersArticles[0]['maj']) ?
              $derniersArticles[0]['maj']
            : date('Y-m-d H:i:s', time()),
);

$smarty->assign('billets', $derniersArticles);
$smarty->assign('feed',    $feed);

header('Content-Type: text/xml; charset=UTF-8');
$smarty->display('rss.xml');