<?php

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Aperos.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Aperos_Html.php';
require_once dirname(__FILE__) .'/../../../sources/Afup/AFUP_Aperos_Inscrit.php';

$erreurs = "";

if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case "login":
			$inscrit = new AFUP_Aperos_Inscrit($bdd);
			if ($inscrit->authentifier($_POST['pseudo'], $_POST['mot_de_passe'])) {
				$inscrit->mettreEnSession($_POST['pseudo']);
			} else {
				$erreurs .= "Désolé mais le compte n'est pas accessible...";
			}
			break;
	}
}

$affichage = new AFUP_Aperos_Html();
$aperos = new AFUP_Aperos($bdd);
$inscrit = new AFUP_Aperos_Inscrit($bdd);
$contenu = "";

if ($inscrit->remplirDepuisSession()) {
	$contenu .= $affichage->zoneLoggedIn($inscrit);
} else {
	$contenu .= $affichage->formulaireLogin();
}

$contenu .= $affichage->listeAperos($aperos->obtenirListe());

$smarty->assign('erreurs', $erreurs);
$smarty->assign('contenu', $contenu);
$smarty->display('index.html');
