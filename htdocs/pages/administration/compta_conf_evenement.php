<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier'));
//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

	
if ($action == 'lister') {
	
	$data = $compta->obtenirListEvenements(true);
	$smarty->assign('data', $data);
	
} elseif ($action == 'ajouter' || $action == 'modifier') {

  	$formulaire = &instancierFormulaire();
	
   if ($action == 'modifier')
   {
        $champsRecup = $compta->obtenirListEvenements(true,$_GET['id']);
        $champs['evenement']          = $champsRecup['evenement'];

		$formulaire->setDefaults($champs);

		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }
   
// facture associé à un évènement
   $formulaire->addElement('header'  , ''                         , '');
	$formulaire->addElement('text', 'evenement', 'Nom Evenement' , array('size' => 30, 'maxlength' => 40));
    

// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

   
    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();

       
    	if ($action == 'ajouter') {
   			$ok = $compta->ajouterConfEvenement(
            						$valeur['evenement']
            						);
        } else {
   			$ok = $compta->modifierConfEvenement(
           							$valeur['id'],
   			           				$valeur['evenement']
           						
             						);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('l\'écriture a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=compta_conf_evenement&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));   

}

?>
