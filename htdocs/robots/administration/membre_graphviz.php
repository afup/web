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

require_once dirname(__FILE__) . '/../../classes/afup/AFUP_Tags.php';
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
    $cmd = "neato -o ".$img_file." -Tpng -Goverlap=false ".$dot_file;
    shell_exec($cmd);
}

$fp = fopen($img_file, 'rb');
header("Content-Type: image/png");
header("Content-Length: " . filesize($img_file));
fpassthru($fp);
exit;

?>