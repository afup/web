<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier'));
$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);

if ($action == 'lister') {

    // Valeurs par défaut des paramÃtres de tri
  //  $timestamp = $assemblee_generale->obternirDerniereDate();
//    $list_date_assemblee_generale = convertirTimestampEnDate($timestamp);
//    $list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription';

    $list_ordre = 'date';
    $list_sens = 'asc';
    $list_associatif = false;
    $list_filtre = false;
	
  //  $smarty->assign('inscriptions', $forum_inscriptions->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
    
	$journal = $compta->obtenirJournal();
//print_r($journal);

	$smarty->assign('journal', $journal);

//    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'ajouter' || $action == 'modifier') {

  	$formulaire = &instancierFormulaire();
	
   if ($action == 'modifier')
   {
           $champs = $compta->obtenir($_GET['id']);
           $formulaire->setDefaults($champs);
   		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }

           
// facture associé à un évènement
   $formulaire->addElement('header'  , ''                         , 'Sélectionner un Journal');
   $formulaire->addElement('select'  , 'idoperation', 'Type d\'opération *', $compta->obtenirFormesOperations());
   $formulaire->addElement('select'  , 'idevenement', 'Evenement *', $compta->obtenirFormesEvenements());

//detail facture       
   $formulaire->addElement('header'  , ''                         , 'Détail Facture');
  $formulaire->addElement('date'    , 'date_saisie'     , 'Date saisie', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));
   $formulaire->addElement('select'  , 'idcategorie', 'Type de compte *', $compta->obtenirFormesCategories());
  $formulaire->addElement('text', 'nom_frs', 'Nom fournisseurs' , array('size' => 30, 'maxlength' => 40));
   	$formulaire->addElement('text', 'numero', 'Numero facture' , array('size' => 30, 'maxlength' => 40));
   	$formulaire->addElement('textarea', 'description', 'Description', array('cols' => 42, 'rows' => 5));
	$formulaire->addElement('text', 'montant', 'Montant *' , array('size' => 30, 'maxlength' => 40));

//reglement
   $formulaire->addElement('header'  , ''                         , 'Réglement');
   $formulaire->addElement('select'  , 'idmode_regl', 'Réglement', $compta->obtenirFormesReglements());
   $formulaire->addElement('date'    , 'date_reglement'     , 'Date', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));
   $formulaire->addElement('textarea', 'obs_regl'           , 'Observation', array('cols' => 42, 'rows' => 5));
   

// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

   
    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();

$date_ecriture= $valeur['date_saisie']['Y']."-".$valeur['date_saisie']['M']."-".$valeur['date_saisie']['d'] ;
$date_regl=$valeur['date_reglement']['Y']."-".$valeur['date_reglement']['M']."-".$valeur['date_reglement']['d'] ;
       
    	if ($action == 'ajouter') {
   			$ok = $compta->ajouter(
            						$valeur['idoperation'],
            						$valeur['idcategorie'],
            						$date_ecriture,
            						$valeur['nom_frs'],
            						$valeur['montant'],
            						$valeur['description'],
									$valeur['numero'],
									$valeur['idmode_regl'],
									$date_regl,
									$valeur['obs_regl'],
									$valeur['idevenement']
            						);
        } else {
   			$ok = $compta->modifier(
            						$valeur['id'],
            						$valeur['idoperation'],
            						$valeur['idcategorie'],
            						$date_ecriture,
            						$valeur['nom_frs'],
            						$valeur['montant'],
            						$valeur['description'],
									$valeur['numero'],
									$valeur['idmode_regl'],
									$date_regl,
									$valeur['obs_regl'],
									$valeur['idevenement']
            						);
        }

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'article ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification de l\'article ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('l\'article a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=site_articles&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'article');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));   
}

?>
