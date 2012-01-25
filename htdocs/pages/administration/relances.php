<?php

$action = verifierAction(array('lister', 'relancer'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Cotisations.php';
$cotisations = new AFUP_Cotisations($bdd);

if ($action == 'lister') {
    $relances_personne_morales = $cotisations->obtenirListeRelancesPersonnesMorales();
    if (empty($relances_personne_morales)) {
        $relances_personne_morales = null;
    }
    $smarty->assign('relances_personnes_morales', $relances_personne_morales);

    $relances_personne_physiques = $cotisations->obtenirListeRelancesPersonnesPhysiques();
    if (empty($relances_personne_physiques)) {
        $relances_personne_physiques = null;
    }
    $smarty->assign('relances_personnes_physiques', $relances_personne_physiques);
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
            	//ajout d'OlivierLeCorre le 18/06/2007
                //liste des $id_personne provoquant une erreur
                $liste .= $id_personne."-";

                //ligne d'origine
				//break;
            }
        }
    }
    //ajout d'OlivierLeCorre le 18/06/2007
    if($liste===""){
		afficherMessage('Les relances ont été effectuées.', 'index.php?page=relances');
	}else{
		afficherMessage('Toutes les relances n\'ont pas pu être effectuées.<br />'.$liste, 'index.php?page=relances', true);
	}

    //code d'origine
    /*
    if (true === $ok) {
        afficherMessage('Les relances ont été effectuées.', 'index.php?page=relances');
    } else {
        afficherMessage('Toutes les relances n\'ont pas pu être effectuées.', 'index.php?page=relances', true);
    }*/
}
?>