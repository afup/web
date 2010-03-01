<?php
/**
 * Fichier site 'RendezVous'
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category RendezVous
 * @package  RendezVous
 * @group    Pages
 */

// 0. initialisation (bootstrap) de l'application

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// 1. chargement des classes nécessaires

require_once 'Afup/AFUP_Rendez_Vous.php';
require_once 'Afup/AFUP_Logs.php';

// 2. récupération et filtrage des données

AFUP_Logs::initialiser($bdd, 0);

$rendezvous = new AFUP_Rendez_Vous($bdd);
$lister_rendezvous = $rendezvous->obtenirListe();

if (isset($lister_rendezvous) and is_array($lister_rendezvous)) {
	foreach ($lister_rendezvous as &$rendezvous) {
		$rendezvous['date'] = date("d/m/Y", $rendezvous['debut']);
		$rendezvous['debut'] = date("H\hi", $rendezvous['debut']);
		$rendezvous['fin'] = date("H\hi", $rendezvous['fin']);
	}
	$smarty->assign('listerendezvous', $lister_rendezvous);
	$smarty->display('archives-rendezvous.html');

} else {
	$smarty->display('pas-de-rendezvous.html');
}