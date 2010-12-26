<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer'));
$tris_valides = array('date', 'organisateur', 'ville');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Aperos.php';
$aperos = new AFUP_Aperos($bdd);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Aperos_Inscrits.php';
$inscrits = new AFUP_Aperos_Inscrits($bdd);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Aperos_Villes.php';
$villes = new AFUP_Aperos_Villes($bdd);

if ($action == 'lister') {

    // Valeurs par dfaut des paramtres de tri
    $list_ordre = 'date DESC';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramtres de tri en fonction des demandes passes en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    
    // Mise en place de la liste dans le scope de smarty
    $evenements = $aperos->obtenirListe($list_ordre, $list_associatif, $list_filtre);
    $smarty->assign('evenements', $evenements);

} elseif ($action == 'supprimer') {
    if ($aperos->supprimer($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'apéro ' . $_GET['id']);
        afficherMessage('L\'apéro a été supprimé', 'index.php?page=aperos&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'apéro', 'index.php?page=aperos&action=lister', true);
    }

} else {
    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
    	$champs['date'] = time();
        $formulaire->setDefaults(array('etat' => 0, 'date' => time()));
    } else {
        $champs = $aperos->obtenir($_GET['id']);
        $formulaire->setDefaults($champs);    
		$formulaire->setDefaults(array('participants' => array_keys($aperos->obtenirListeParticipants($_GET['id']))));
    }

    $formulaire->addElement('header', '', 'Informations');
	$formulaire->addElement('date', 'date', 'Date', array('language' => 'fr', 'format' => "dMY H:i", 'minYear' => min(date('Y') - 10, date('Y', $champs['date'])), 'maxYear' => max(date('Y') + 1, date('Y', $champs['date'])), 'optionIncrement' => array('i' => 15)));
	$formulaire->addElement('select', 'id_organisateur', 'Organisateur', array(0 => '--') + $inscrits->obtenirSelect('pseudo ASC', true));
    $formulaire->addElement('select', 'id_ville', 'Ville', array(0 => '--') + $villes->obtenirListe('nom ASC', true));
    $formulaire->addElement('textarea', 'lieu', 'Lieu');

    $formulaire->addElement('header', '', 'Paramètres');
    $formulaire->addElement('select', 'etat', 'Etat', $aperos->obtenirListeEtat());
    if (isset($_GET['id']) and $_GET['id'] > 0) {
		$element =& $formulaire->addElement('altselect', 'participants', 'Participants', $inscrits->obtenirSelect('pseudo ASC'));
		$element->setMultiple(true);
    }
    
    $formulaire->addElement('header', 'boutons', '');
    $formulaire->addElement('submit', 'soumettre', ucfirst($action));
    
    if ($formulaire->validate()) {
		$date = $formulaire->exportValue('date');
		$date = mktime($date['H'], $date['i'], 0, $date['M'], $date['d'], $date['Y']);

		if ($action == 'ajouter') {
            $ok = $aperos->ajouter($formulaire->exportValue('id_organisateur'),
                                   $formulaire->exportValue('id_ville'),
                                   $date,
                                   $formulaire->exportValue('lieu'),
                                   $formulaire->exportValue('etat'));
        } else {
            $ok = $aperos->modifier($_GET['id'],
                                    $formulaire->exportValue('id_organisateur'),
                                    $formulaire->exportValue('id_ville'),
                                    $date,
                                    $formulaire->exportValue('lieu'),
                                    $formulaire->exportValue('etat'));
			$aperos->modifierParticipants($_GET['id'], $formulaire->exportValue('participants'));
        }
        
        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'apéro du ' . $formulaire->exportValue('date'));
            } else {
                AFUP_Logs::log('Modification de l\'apéro du ' . $formulaire->exportValue('date') . ' (' . $_GET['id'] . ')');
            }            
            afficherMessage('L\'apéro a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=aperos&action=lister');    
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'apéro');    
        }    
    } 
    
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}
