<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier'));
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);


if ($action == 'lister') {

	$data = $compta->obtenirListComptes(true);
	$smarty->assign('data', $data);



} elseif ($action == 'ajouter' || $action == 'modifier') {

  	$formulaire = &instancierFormulaire();

   if ($action == 'modifier')
   {
        $champsRecup = $compta->obtenirListCategories('',$_GET['id']);
        $champs['categorie']          = $champsRecup['categorie'];

		$formulaire->setDefaults($champs);

		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }

// partie saisie
   $formulaire->addElement('header'  , ''                         , '');
	$formulaire->addElement('text', 'nom_compte', 'Compte' , array('size' => 30, 'maxlength' => 40));


// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));


    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();


    	if ($action == 'ajouter') {
   			$ok = $compta->ajouterConfig(
   									'compta_compte',
   									'nom_compte',
            						$valeur['nom_compte']
            						);
        } else {
   			$ok = $compta->modifierConfig(
   									'compta_compte',
           							$valeur['id'],
           							'nom_compte',
   			           				$valeur['nom_compte']
             						);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('l\'écriture a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=compta_conf_compte&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));

}
