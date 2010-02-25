<?php
$lancement = microtime(TRUE);

require dirname(__FILE__) . '/../../classes/afup/AFUP_Configuration.php';
$conf = new AFUP_Configuration(dirname(__FILE__) . '/../../include/configuration.inc.php');

require dirname(__FILE__) . '/../../classes/afup/AFUP_Base_De_Donnees.php';
$bdd = new AFUP_Base_De_Donnees($conf->obtenir('bdd|hote'),
                                $conf->obtenir('bdd|base'),
                                $conf->obtenir('bdd|utilisateur'),
                                $conf->obtenir('bdd|mot_de_passe'));

require dirname(__FILE__) . '/../../classes/afup/AFUP_Logs.php';
AFUP_Logs::initialiser($bdd, 0);

define('MAGPIE_CACHE_DIR', dirname(__FILE__).'/../../cache/robots/planete');
require dirname(__FILE__) . '/../../classes/magpierss/rss_fetch.inc';

require dirname(__FILE__) . '/../../classes/afup/AFUP_Planete_Flux.php';
$planete_flux = new AFUP_Planete_Flux($bdd);

require dirname(__FILE__) . '/../../classes/afup/AFUP_Planete_Billet.php';
$planete_billet = new AFUP_Planete_Billet($bdd);

$flux = $planete_flux->obtenirListeActifs();

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

$duree = round(microtime(TRUE) - $lancement, 2);
AFUP_Logs::log('Exploration de ' . count($flux). ' flux -- ' . ($erreurs) . ' erreur(s) -- en ' . $duree . 's');

?>