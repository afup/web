<?php

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Utils.php';
$droits = AFUP_Utils::fabriqueDroits($bdd);

if (!$droits->estConnecte() ) {
   header('Location: index.php?page=connexion&echec=' . $droits->verifierEchecConnexion());
   exit;
}

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_id = $forum->obtenirDernier();
$id_personne = isset($_GET['id_personne']) ? (int)$_GET['id_personne'] : 0;
$badges = $forum_inscriptions->obtenirListePourBadges($forum_id, $id_personne);
$badge_prints =array();
$nb_cols = 2;
$nb_rows = 4;
$badge_row = 1;
$badge_col = 1;
$badge_page = 1;

$nombre = count($badges);
while (count($badges) % $nb_cols != 0) {
	$badges[] = array(
		'prenom' => "__",
		'nom' => "__",
		'commentaires' => "",
		'type_inscription' => AFUP_FORUM_2_JOURNEES,
		'societe' => "",
		'tags' => "__",
	);
}

$i = 0;
$html = "";
foreach ($badges as $nb => $badge) {
	if ($i % ($nb_rows * $nb_cols) == 0) {
		$html .= '<table class="programme">';
	}
	
	if ($i % $nb_cols == 0) {
		$html .= '<tr>
					<th><img src="'.$conf->obtenir('web|path').'/templates/administration/images/badges-croix.png" /></th>';
		for ($j = 0; $j < $nb_cols; $j++) {
			$html .= '<th><img src="'.$conf->obtenir('web|path').'/templates/administration/images/badges-croix.png" /></th>';
		}
		$html .= '</tr>
				<tr>
					<td></td>';
	}
	
	preg_match('@\<tag\>(.*)\</tags\>@i', $badge['commentaires'], $matches);
	$tags =  isset($matches[1]) ? $matches[1] : '';
	$tags = explode(';', $tags);
	$badge['tags'] = array();
	foreach ($tags as $tag) {
		if (!empty($tag)) {
			$badge['tags'][] = $tag;
		}
	}
	$badge['tags'] = substr(join(" - ", $badge['tags']), 0, 45);
	$lib_pass = $AFUP_Tarifs_Forum_Lib[$badge['type_inscription']];
	switch ($badge['type_inscription']) {
		case AFUP_FORUM_PREMIERE_JOURNEE:
			$lib_pass = 'PASS JOUR 1';
			break;
		case AFUP_FORUM_DEUXIEME_JOURNEE:
			$lib_pass = 'PASS JOUR 2';
			break;
		case AFUP_FORUM_2_JOURNEES:
		case AFUP_FORUM_2_JOURNEES_AFUP:
		case AFUP_FORUM_2_JOURNEES_ETUDIANT:
		case AFUP_FORUM_2_JOURNEES_PREVENTE:
		case AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE:
		case AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE:
		case AFUP_FORUM_2_JOURNEES_COUPON:
		case AFUP_FORUM_INVITATION:
			$lib_pass = 'PASS 2 JOURS';
			break;
		case AFUP_FORUM_ORGANISATION:
		case AFUP_FORUM_PRESSE:
		case AFUP_FORUM_CONFERENCIER:
		case AFUP_FORUM_SPONSOR:
			$lib_pass = strtoupper($AFUP_Tarifs_Forum_Lib[$badge['type_inscription']]);
			break;
		default:
			break;
	}
	$badge['type_pass'] = $lib_pass;

	$html .= '
		<td>
			<div class="badge">
				<img src="'.$conf->obtenir('web|path').'/templates/administration/images/badge-entete-'.$forum_id.'.png" alt=""  style="padding-top:10px;"/>
				<div>
					<div>
					    <div class="nom-prenom">
					    	'.ucfirst(strtolower($badge['prenom'])).' '.strtoupper(str_replace(array('é'), array('e'), $badge['nom'])).'
					    </div>
					    <div class="societe">'.$badge['societe'].'</div>
					    <div class="pass">'.$badge['type_pass'].'</div>
					    <div class="tags">'.$badge['tags'].'</div>
						<div class="liens">Twitter : @afup - Hash #phptour | Programme : afup.org/m</div>
					</div>
				</div>
			</div>
		</td>';
	
	if ($i % $nb_cols == $nb_cols - 1) {
		$html .= '
				<td></td>
			</tr>';
	}
	
	if ($i % ($nb_rows * $nb_cols) == ($nb_rows * $nb_cols) - 1) {
		$html .= '</table>';
	}
	
	
	$i++;
}

if (!preg_match("/<\/table>$/", $html)) {
	$html .= '</table>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>AFUP - badges</title>
<style media="all">
div.badge {
  width: 550px;
  height: 330px;
  display: overlay;
  border: 0px solid red;
  text-align: center;
  overflow: hidden;
}

div.badge div.nom-prenom { font-size: 30px; margin-top: 20px; font-weight: bold; }
div.badge div.societe { font-size: 20px; }
div.badge div.pass { margin: 20px; }
div.badge div.tags { font-weight: bold; font-size: 20px; }
div.badge div.liens { margin-top: 20px; vertical-align: bottom; font-size: 20px; }

th { vertical-align: top; text-align: right; }

table { page-break-after: always; padding: 0px; width: 100%; }

</style>
</head>

<body style="margin: 0; padding: 0px; ">
<?php

echo $html;

die();

?>
</body>
</html>
