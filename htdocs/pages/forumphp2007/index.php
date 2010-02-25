<?php
require_once '../../include/prepend.inc.php';
$smarty->caching = false;

$aujourdhui = time();
$date_forum = mktime(0, 0, 0, 11, 21, 2007);
$jours_avant_forum = ceil(($date_forum - $aujourdhui) / 86400);

if ($jours_avant_forum > 0) {
	$alerte_avant_forum = "<fieldset>";
	$alerte_avant_forum .= "<legend>&nbsp;Inscriptions fermées !&nbsp;</legend>";
	$alerte_avant_forum .= "<h3>Les inscriptions sont désormais fermées.<br /> Rendez-vous l'année prochaine.</h3>";
	$alerte_avant_forum .= "</fieldset>";
} else {
	$alerte_avant_forum = "";
}
$smarty->assign('alerte_avant_forum', $alerte_avant_forum);

$infos_importantes = array();

$info_importante['titre'] = "Le podcast !";
$info_importante['contenu'] = "<p>Cette année l'AFUP vous propose les podcast des conférences&nbsp;! ".
" Parfait si vous désirez les réécouter en regardant les slides ou si vous n'avez pu vous rendre au Forum.</p>".
"<p>Tout se passe à l'adresse suivante : <a href=\"http://feeds.feedburner.com/forumphp2007\" title=\"flux des podcasts\">http://feeds.feedburner.com/forumphp2007</a>.</p><p>En allant directement sur cette page vous pourrez ajouter le flux à votre lecteur. Le podcast est une réalisation <a href=\"http://www.tryphon.org\">http://www.tryphon.org</a></p>";
$infos_importantes[] = $info_importante;

$info_importante['titre'] = "Aperçu vidéo";
$info_importante['contenu'] = 
'<p><object type="application/x-shockwave-flash" data="/pages/video/player_flv.swf" height="252" width="330">
  <param name="movie" value="/pages/video/player_flv.swf" height="252" width="330" />
  <param name=FlashVars value="flv=/pages/video/forumphp2007.flv&amp;showstop=1&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;playercolor=eeeeee&amp;title=Lecture_video&amp;height=252&amp;width=330" />
  <param name="wmode" value="transparent" />
</object>
</p>'."<p>Vous avez raté le Forum PHP 2007 ? ".
"Pour très bientôt un <a href='resumes.php'>résumé complet avec les présentations</a> des conférenciers.</p>";
$infos_importantes[] = $info_importante;
//   <param name="FlashVars" value="flv=http://www.afup.org/pages/video/forumphp2007.flv&amp;showstop=1&amp;autoplay=0&amp;bgcolor1=ffffff&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;playercolor=eeeeee&amp;title=Lecture_video&amp;height=252&amp;width=330" />
$info_importante['titre'] = "Eyrolles offre des livres !";
$info_importante['contenu'] = "<p>Notre partenaire Eyrolles offre <a href='http://www.eyrolles.com/Accueil/Livre/9782212121674/livre-php-5-avance.php'>dix exemplaires de son best seller PHP 5 avancé</a> ainsi que cinq exemplaires du livre <a href='http://www.eyrolles.com/Informatique/Livre/9782212121148/livre-securite-php-5-et-mysql.php'>Sécurité PHP 5</a>.</p>".
"</p><p align='center'><a href='http://www.eyrolles.com/Accueil/Livre/9782212121674/livre-php-5-avance.php'><img src='http://www.afup.org//templates/forumphp2007/images/livre-php5-avance.gif' border=0></a>" .
" <a href='http://www.eyrolles.com/Informatique/Livre/9782212121148/livre-securite-php-5-et-mysql.php'><img src='http://www.afup.org//templates/forumphp2007/images/livre-php5securite.gif' border=0></a></p>".
"<p>Les livres seront offert par tirage au sort lors des conférences.</p>";
$infos_importantes[] = $info_importante;

$smarty->assign('infos_importantes', $infos_importantes);

$actualites = array();
$actualite['titre'] = "Rendez-vous en 2008";
$actualite['contenu'] = "<p>La page est tournée pour le Forum PHP 2007.</p>".
"<p>Merci aux quelques 450 visiteurs qui ont su braver les grèves pour venir écouter les meilleurs conférenciers PHP francophones et internationaux.</p>";
$actualite['date'] = "22/11/2007";
$actualites[] = $actualite;

$smarty->assign('actualites', $actualites);

$smarty->display('index.html');
