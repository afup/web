<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Site.php';
$articles = new AFUP_Site_Articles($bdd);
$derniers_articles = $articles->chargerDerniersAjouts(20);

$derniers_articles_array = array();
$i = 0;
foreach($derniers_articles as $article) {
  $derniers_articles_array[$i]["titre"] = $article->titre;
  $derniers_articles_array[$i]["contenu"] = $article->contenu;
  $derniers_articles_array[$i]["url"] = $article->route;
  $derniers_articles_array[$i]["maj"] = date(DATE_RSS,$article->date);
  $i++;
}


header('Content-Type: text/xml; charset=UTF-8');

$feed = array();
$feed['title'] = "Le flux RSS de l'AFUP";
$feed['url'] = 'http://afup.org/';
$feed['link'] = $feed['url'].'rss.php';
$feed['email'] = "bureau@afup.org";
$feed['author'] = "Nicolas Silberman / AFUP";
$feed['date'] = date(DATE_RSS);
$feed['lastBuildDate'] = $derniers_articles_array[0]["maj"];

$smarty->assign('feed', $feed);

$smarty->assign('billets', $derniers_articles_array);

$smarty->display('rss.xml');

?>