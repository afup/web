<?php

// Impossible to access the file itself
use Afup\Site\Association\Votes;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier'));
$smarty->assign('action', $action);


$votes = new Votes($bdd);

if ($action == 'lister') {
	$list_champs = '*';
    $list_ordre = 'lancement';
    $list_sens = 'desc';
    $list_filtre = false;
    $smarty->assign('votes', $votes->obtenirListe($list_champs, $list_ordre, $list_filtre));

} else {

    $votes = new Votes($bdd);

    $formulaire = &instancierFormulaire();

    if (isset($_GET['id'])) {
    	$champs = $votes->obtenir($_GET['id']);
    	$formulaire->setDefaults($champs);
    }
    
    $formulaire->addElement('header' , '' , 'Informations');
    $formulaire->addElement('date', 'lancement', 'Lancement', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));
    $formulaire->addElement('date', 'cloture', 'Clôture', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));
    $formulaire->addElement('textarea' , 'question' , 'Question' , array('cols' => 42, 'rows' => 10));
    
    $formulaire->addElement('header' , 'boutons' , '');
    $formulaire->addElement('submit' , 'soumettre' , ucfirst($action));

    $formulaire->addRule('question' , 'Question manquante' , 'required');

    if ($formulaire->validate()) {
        if ($action == 'ajouter') {
        	$lancement =  $formulaire->exportValue('lancement');
        	$lancement = mktime(0, 0, 0, $lancement['M'], $lancement['d'], $lancement['Y']);
        	
        	$cloture =  $formulaire->exportValue('cloture');
        	$cloture = mktime(23, 59, 59, $cloture['M'], $cloture['d'], $cloture['Y']);
        	
            if ($votes->ajouter($formulaire->exportValue('question'), $lancement, $cloture, time())) {
	            Logs::log('Ajout du vote ' . $formulaire->exportValue('question'));
	            afficherMessage('Le vote a été ajoutée', 'index.php?page=votes&action=lister');
            } else {
            	$smarty->assign('erreur', 'Une erreur est survenue lors de l\'ajout du vote');
            }

        } else {
        	$lancement =  $formulaire->exportValue('lancement');
        	$lancement = mktime(0, 0, 0, $lancement['M'], $lancement['d'], $lancement['Y']);
        	
        	$cloture =  $formulaire->exportValue('cloture');
        	$cloture = mktime(23, 59, 59, $cloture['M'], $cloture['d'], $cloture['Y']);
            
            if ($votes->modifier($_GET['id'], $formulaire->exportValue('question'), $lancement, $cloture, time())) {
            	Logs::log('Modification du vote ' . $formulaire->exportValue('question') . ' (' . $_GET['id'] . ')');
            	afficherMessage('Le vote a été modifiée', 'index.php?page=votes&action=lister');
            } else {
            	$smarty->assign('erreur', 'Une erreur est survenue lors de l\'ajout du vote');
            }
        }
    }

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}