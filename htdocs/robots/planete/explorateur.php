<?php
/**
 * Script d'exploration des feeds pour le site PlanetePHP.
 * 
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 * 
 * @category PlanetePHP
 * @package  PlanetePHP
 * @group    Batchs
 */

require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/Cli.php';

define('MAGPIE_CACHE_DIR', dirname(__FILE__).'/../../cache/robots/planete');

require_once dirname(__FILE__) . '/../../../dependencies/magpierss/rss_fetch.inc';
require_once dirname(__FILE__) . '/../../../sources/Afup/AFUP_Planete_Flux.php';
require_once dirname(__FILE__) . '/../../../sources/Afup/AFUP_Planete_Billet.php';

$planete_flux   = new AFUP_Planete_Flux($bdd);
$planete_billet = new AFUP_Planete_Billet($bdd);
$flux           = $planete_flux->obtenirListeActifs();

$billets = 0;

foreach ($flux as $flux_simple) {
    $rss = fetch_rss($flux_simple['feed']);
	$rss->items = array_reverse($rss->items);
	foreach ($rss->items as $item) {
	    if (empty($item['id'])) {
			$item['id'] = $item['link'];
		}
		if (empty($item['atom_content'])) {
			$item['atom_content'] = $item['summary'];
		}
		if (empty($item['atom_content'])) {
			$item['atom_content'] = $item['content'];
		}
		if ($item['atom_content'] == "A") {
		    $item['atom_content'] = $item['description'];
		}
		if (empty($item['updated'])) {
			$item['updated'] = $item['dc']['date'];
		}
		if (empty($item['updated'])) {
			$item['updated'] = $item['modified'];
		}
		if (empty($item['updated'])) {
			$item['updated'] = $item['pubdate'];
		}

		$item['timestamp'] = strtotime($item['updated']);
		if ($item['timestamp'] > time() - 7 * 24 * 3600 ) {
			$contenu = $item['title']." ".$item['atom_content'];
			$item['etat'] = $planete_billet->avecContenuPertinent($contenu);
			$succes += $planete_billet->sauvegarder($flux_simple['id'],
													 $item['id'],
													 $item['title'],
													 $item['link'],
													 $item['timestamp'],
													 $item['author'],
													 $item['summary'],
													 $item['atom_content'],
													 $item['etat']);
			$billets++;
		}

	}
}
$erreurs = $billets - $succes;

$duree = round(microtime(TRUE) - $startMicrotime, 2);
AFUP_Logs::log('Exploration de ' . count($flux). ' flux -- ' . ($erreurs) . ' erreur(s) -- en ' . $duree . 's');
