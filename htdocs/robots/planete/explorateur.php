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

use Afup\Site\Planete\Flux;
use Afup\Site\Planete\Planete_Billet;
use Afup\Site\Utils\Logs;

require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/Cli.php';

define('MAGPIE_CACHE_DIR', dirname(__FILE__).'/../../../var/cache/prod/planete');
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

require_once dirname(__FILE__) . '/../../../dependencies/magpierss/rss_fetch.inc';

$planete_flux   = new Flux($bdd);
$planete_billet = new Planete_Billet($bdd);
$flux           = $planete_flux->obtenirListeActifs();

$billets = $succes = 0;
foreach ($flux as $flux_simple) {
	echo $flux_simple['feed']." : début...<br />\n";
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
		if (empty($item['updated']) && isset($item['dc']['date'])) {
			$item['updated'] = $item['dc']['date'];
		}
		if (empty($item['updated']) && isset($item['modified'])) {
			$item['updated'] = $item['modified'];
		}
		if (empty($item['updated']) && isset($item['pubdate'])) {
			$item['updated'] = $item['pubdate'];
		}
		if (empty($item['author'])) {
			$item['author'] = $flux_simple['nom'];
		}

		$item['timestamp'] = strtotime($item['updated']);
		if ($item['timestamp'] > time() - 7 * 24 * 3600 ) {
			echo ' - contenu récent : "' . $item['title'] . '"' . PHP_EOL;
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
	echo $flux_simple['feed']." : fin !<br /><br/>\n\n";
}
$erreurs = $billets - $succes;

$duree = round(microtime(TRUE) - $startMicrotime, 2);
Logs::log('Exploration de ' . count($flux). ' flux -- ' . ($erreurs) . ' erreur(s) -- en ' . $duree . 's');
