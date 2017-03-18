<?php

// Impossible to access the file itself
use Afup\Site\Association\Cotisations;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'relancer'));
$smarty->assign('action', $action);


$cotisations = new Cotisations($bdd);

if ($action == 'lister') {
    $relances_personne_morales = $cotisations->obtenirListeRelancesPersonnesMorales();
    if (empty($relances_personne_morales)) {
        $relances_personne_morales = null;
    }
    $smarty->assign('relances_personnes_morales', $relances_personne_morales);

    $kernel = new \Afup\Site\Utils\SymfonyKernel();

    $page = isset($_GET['pagenum']) ? (int)$_GET['pagenum'] : 1;
    if ($page === 0) {
        $page = 1;
    }

    $request = $kernel->getRequest('/admin/association/relances/' . $page);
    $response = $kernel->getResponse();

    $smarty->assign('sfContent', $response->getContent());
    $kernel->getKernel()->terminate($request, $response);

} elseif ($action == 'relancer') {
    $donnees = array_keys($_POST);
    $ok = true;
    $liste="";
    for ($i = 0, $taille = count($donnees); $i < $taille; $i++) {
        if (FALSE !== strpos($donnees[$i], "_")) {
            $type_personne = substr($donnees[$i], 0, 1);
            $id_personne   = substr($donnees[$i], 2);
            $ok = $cotisations->relancer($type_personne, $id_personne);
            if (false === $ok) {
                $liste .= $id_personne."-";
            }
        }
    }
    if($liste===""){
		afficherMessage('Les relances ont été effectuées.', 'index.php?page=relances');
	}else{
		afficherMessage('Toutes les relances n\'ont pas pu être effectuées.<br />'.$liste, 'index.php?page=relances', true);
	}
}
