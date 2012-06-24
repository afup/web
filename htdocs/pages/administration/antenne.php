<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Antenne.php';
$antenne = new AFUP_Antenne($bdd);


if ($action == 'lister') {

	$data = $antenne->obtenirListAntennes(true);
	$smarty->assign('data', $data);


} elseif ($action == 'ajouter' || $action == 'modifier') {

  	$formulaire = &instancierFormulaire();

   if ($action == 'modifier')
   {
        $champsRecup = $antenne->obtenirListAntennes('',$_GET['id']);
        $champs['ville']          = $champsRecup['ville'];

		$formulaire->setDefaults($champs);

		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }

// partie saisie
   $formulaire->addElement('header'  , ''                         , '');
	$formulaire->addElement('text', 'ville', 'Ville' , array('size' => 30, 'maxlength' => 40));


// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));


    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();


    	if ($action == 'ajouter') {
   			$ok = $antenne->ajouter(
   									'afup_antenne',
            						'ville',
   									$valeur['ville']
            						);
        } else {
   			$ok = $antenne->modifier(
   									'afup_antenne',
           							$valeur['id'],
            						'ville',
   									$valeur['ville']
             						);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout une antenne ' . $formulaire->exportValue('ville'));
            } else {
                AFUP_Logs::log('Modification une antenne ' . $formulaire->exportValue('ville') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'antenne a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=antenne_conf_operation&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));

}

?>
