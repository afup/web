<?php

// Impossible to access the file itself
use Afup\Site\Association\Votes;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
	trigger_error("Direct access forbidden.", E_USER_ERROR);
	exit;
}

$action = verifierAction(array('lister', 'voter', 'consulter'));
$smarty->assign('action', $action);


$votes = new Votes($bdd);

if ($action == 'lister') {
    $list_champs = '*';
    $list_ordre = 'lancement';
    $list_sens = 'desc';
    $list_filtre = false;
    $smarty->assign('votes', $votes->obtenirListeVotesOuverts(time(), $list_champs, $list_ordre, $list_filtre));

} else {

    $votes = new Votes($bdd);

    $formulaire = &instancierFormulaire();

    if (isset($_GET['id'])) {
   		$champs = $votes->obtenirPoids($_GET['id'], $droits->obtenirIdentifiant());
    	$formulaire->setDefaults($champs);
    	
        $formulaire->addElement('header', null, 'Poids');
	    $liste_poids = $votes->obtenirListePoids((int)$_GET['id']);
	    if (is_array($liste_poids)) {
	    	$poids_total = 0;
		    foreach ($liste_poids as $poids) {
		        $formulaire->addElement('static',
		        						'poids_'.$poids['id_vote'].'-'.$poids['id_personne_physique'],
		                                date('d/m/Y h:i', $poids['date']),
		                                $poids['commentaire'].' ('.$poids['personne_physique'].')<br /><strong>'.$poids['poids'].'</strong>');
		        $poids_total += $poids['poids'];
		    }
		    $formulaire->addElement('static', 'poids_total', 'Résultat total', '<strong>'.$poids_total.'</strong>');
	    }
    	
		$formulaire->addElement('header'  , ''         , 'Voter');

	    $vote = $votes->obtenir($_GET['id']);
		$formulaire->addElement('static'  , 'note'     , ' ', $vote['question']);
	    $formulaire->addElement('textarea' , 'commentaire' , 'Commentaire' , array('cols' => 42, 'rows' => 10));
	    $formulaire->addElement('select' , 'poids' , 'Poids' , array('' => "--", -1 => "-1", 0 => "0", 1 => "+1"));
	        
	    $formulaire->addElement('header' , 'boutons' , '');
	    $formulaire->addElement('submit' , 'voter' , ucfirst($action));
	
	    if ($formulaire->validate()) {
	    	if ($action == 'voter') {
	    		if ($votes->voter((int)$_GET['id'], $droits->obtenirIdentifiant(), $formulaire->exportValue('commentaire'), $formulaire->exportValue('poids'), time())) {
	    			Logs::log('Ajout du poids sur le vote ' . (int)$_GET['id'].' - "'.$vote['question'].'"');
	    			afficherMessage('Le poids sur le vote a été ajoutée', 'index.php?page=membre_votes&action=lister');
	    		} else {
	    			$smarty->assign('erreur', 'Une erreur est survenue lors de l\'ajout du poids sur le vote');
	    		}
	    	}
	    }
    }
    

    $smarty->assign('formulaire', genererFormulaire($formulaire));
}