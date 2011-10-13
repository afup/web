<?php
/**
 * Script de génération du graphe de tag des membres
 *
 * @author    Perrick Penet   <perrick@noparking.fr>
 * @author    Olivier Hoareau <olivier@phppro.fr>
 * @copyright 2010 Association Française des Utilisateurs de PHP
 *
 * @category Administration
 * @package  Administration
 * @group    Batchs
 */

// chargement du fichier d'initialisation du contexte ligne de commande

require_once dirname(__FILE__) . '/../../../sources/Afup/Bootstrap/Cli.php';

// logique interne du script

require_once 'Afup/AFUP_Tags.php';
$tags = new AFUP_Tags($bdd);

if (isset($_GET['membres'])) {
    $dot_file = dirname(__FILE__) . '/membre_graphviz.membres.dot';
    $img_file = dirname(__FILE__) . '/membre_graphviz.membres.png';
} else {
    $dot_file = dirname(__FILE__) . '/membre_graphviz.tags.dot';
    $img_file = dirname(__FILE__) . '/membre_graphviz.tags.png';
}

if (!file_exists($dot_file) or !file_exists($img_file) or fileatime($dot_file) < time() - 3600) {
	if (isset($_GET['membres'])) {
	    $noeuds = $tags->obtenirNoeudsPersonnesPhysiqyes();
	} else {
	    $noeuds = $tags->obtenirNoeudsTags();
	}
	file_put_contents($dot_file, $tags->preparerFichierDot($noeuds));
    $cmd = "neato -o ".$img_file." -Tpng -Goverlap=false -Gcharset=latin1 ".$dot_file;
    shell_exec($cmd);
}

$fp = fopen($img_file, 'rb');
header("Content-Type: image/png");
header("Content-Length: " . filesize($img_file));
fpassthru($fp);
exit;