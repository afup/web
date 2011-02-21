<?php
$action = verifierAction(array(
					'lister', 
					'modifier',
					'telecharger_facture', 
					'envoyer_facture'
					));

					
//$action = verifierAction(array('lister', 'devis','facture','ajouter', 'modifier'));
//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc'); 
$smarty->assign('action', $action);


require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta_Facture.php';
$comptaFact = new AFUP_Compta_Facture($bdd);
	
if ($action == 'lister') {
	$ecritures = $comptaFact->obtenirFacture();
	$smarty->assign('ecritures', $ecritures);
} elseif ($action == 'telecharger_devis') {
	$comptaFact->genererDevis($_GET['ref']);
} elseif ($action == 'telecharger_facture') {
	$comptaFact->genererFacture($_GET['ref']);
} elseif ($action == 'envoyer_facture'){
	if($comptaFact->envoyerFacture($_GET['ref'])){
		AFUP_Logs::log('Envoi par email de la facture n°' . $_GET['ref']);
		afficherMessage('La facture a été envoyée', 'index.php?page=forum_facturation&action=lister');
	} else {
		afficherMessage("La facture n'a pas pu être envoyée", 'index.php?page=forum_facturation&action=lister', true);
	}
} elseif ($action == 'envoyer_devis'){
	if($comptaFact->envoyerDevis($_GET['ref'])){
		AFUP_Logs::log('Envoi par email de la devis n°' . $_GET['ref']);
		afficherMessage('Le devis a été envoyé', 'index.php?page=compta_devis&action=lister');
	} else {
		afficherMessage("Le devis n'a pas pu être envoyé", 'index.php?page=compta_devis&action=lister', true);
	}
} elseif ($action == 'ajouter' || $action == 'modifier') {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);
	
  	$formulaire = &instancierFormulaire();
	
   if ($action == 'modifier')
   {
        $champsRecup = $comptaFact->obtenir($_GET['id']);

        $champs['date_saisie']          = $champsRecup['date_ecriture'];
        $champs['societe']          = $champsRecup['societe'];
        $champs['reference']          = $champsRecup['reference'];
        $champs['societe']          = $champsRecup['societe'];
        $champs['service']          = $champsRecup['service'];
        $champs['adresse']          = $champsRecup['adresse'];
        $champs['code_postal']          = $champsRecup['code_postal'];
        $champs['ville']          = $champsRecup['ville'];
        $champs['id_pays']          = $champsRecup['id_pays'];
        $champs['email']          = $champsRecup['email'];
        $champs['observation']          = $champsRecup['observation'];
        $champs['ref_clt1']          = $champsRecup['ref_clt1'];
        $champs['ref_clt2']          = $champsRecup['ref_clt2'];
        $champs['ref_clt3']          = $champsRecup['ref_clt3'];
         
		$formulaire->setDefaults($champs);
		//$formulaire->setDefaults($champsRecup);
		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }
   
//detail devis       
   $formulaire->addElement('header'  , ''                         , 'Détail Devis');
   
//$mois=10;
   $formulaire->addElement('date'    , 'date_saisie'     , 'Date saisie', array('language' => 'fr', 
                                                                                'format'   => 'd F Y',
  																				'minYear' => date('Y'), 
  																				'maxYear' => date('Y')));

	$formulaire->addElement('header'  , ''                       , 'Facturation');
	$formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
	$formulaire->addElement('text'    , 'societe'    , 'Société'        , array('size' => 50, 'maxlength' => 100));
	$formulaire->addElement('text'    , 'service'        , 'Service'            , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('textarea', 'adresse'    , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
	$formulaire->addElement('text'    , 'code_postal', 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
	$formulaire->addElement('text'    , 'ville'      , 'Ville'          , array('size' => 30, 'maxlength' => 50));
	$formulaire->addElement('select'  , 'id_pays'    , 'Pays'           , $pays->obtenirPays());
	$formulaire->addElement('text'    , 'email'      , 'Email (facture)', array('size' => 30, 'maxlength' => 100));

	$formulaire->addElement('header', null          , 'Réservé à l\'administration');
	$formulaire->addElement('static'  , 'note'                   , ''               , 'La reference est utilisée comme numéro de facture. Elle peut être commune à plusieurs inscriptions...<br /><br />');
	$formulaire->addElement('text'  , 'reference'   , 'Référence'   , array('size' => 50, 'maxlength' => 100));
	
	$formulaire->addElement('header', null          , 'Référence client');
	$formulaire->addElement('static'  , 'note'  , '', 'Possible d\'avoir plusieurs références à mettre (obligation client)<br /><br />');
	$formulaire->addElement('text'  , 'ref_clt1'   , 'Référence client'   , array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'ref_clt2' , 'Référence client 2', array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'ref_clt3' , 'Référence client 3' , array('size' => 50, 'maxlength' => 100));


	  
   $formulaire->addElement('header'  , '', 'Observation');
	$formulaire->addElement('static'  , 'note'     , ''  , 'Ces informations seront écrites à la fin du document<br /><br />');
   $formulaire->addElement('textarea', 'observation'  , 'Observation', array('cols' => 42, 'rows' => 5));
  

   
   
  $formulaire->addElement('header'  , '', 'Contenu');
	$formulaire->addElement('text'    , 'ref'    , 'Référence'        , array('size' => 50, 'maxlength' => 100));
	$formulaire->addElement('textarea', 'designation'  , 'Désignation', array('cols' => 42, 'rows' => 5));
	$formulaire->addElement('text'    , 'quantite'    , 'Quantite'        , array('size' => 50, 'maxlength' => 100));
	$formulaire->addElement('text'    , 'pu'    , 'Prix Unitaire'        , array('size' => 50, 'maxlength' => 100));
	
  
   
   
   
   
// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

// ajoute des regles
//	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'required');
//	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'nonzero');
	$formulaire->addRule('societe'       , 'Société manquant'      , 'required');
	$formulaire->addRule('adresse'       , 'Adresse manquant'      , 'required');
	$formulaire->addRule('email'       , 'Email manquant'      , 'required');
	
    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();

$date_ecriture= $valeur['date_saisie']['Y']."-".$valeur['date_saisie']['F']."-".$valeur['date_saisie']['d'] ;
      
    	if ($action == 'ajouter') {
   			$ok = $comptaFact->ajouter(
            						$date_ecriture,
            						$valeur['societe'],
            						$valeur['service'],
            						$valeur['adresse'],
									$valeur['code_postal'],
									$valeur['ville'],
									$valeur['id_pays'],
									$valeur['email'],
									$valeur['observation'],
									$valeur['ref_clt1'],
									$valeur['ref_clt2'],
									$valeur['ref_clt3']
            						);
           						
  			$ok = $comptaFact->ajouter_details(
            						$valeur['ref'],
            						$valeur['designation'],
            						$valeur['quantite'],
									$valeur['pu']
            						);
            						
    	} else {
   			$ok = $comptaFact->modifier(
									$_GET['id'],
   									$date_ecriture,
            						$valeur['societe'],
            						$valeur['service'],
            						$valeur['adresse'],
									$valeur['code_postal'],
									$valeur['ville'],
									$valeur['id_pays'],
									$valeur['email'],
									$valeur['observation'],
									$valeur['ref_clt1'],
									$valeur['ref_clt2'],
									$valeur['ref_clt3'],
									$valeur['reference']
									);
  			$ok = $comptaFact->modifier_details(
									$_GET['id'],
  									$valeur['ref'],
            						$valeur['designation'],
            						$valeur['quantite'],
									$valeur['pu']
            						);
    	}

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('l\'écriture a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=compta_devis&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }

       
    $smarty->assign('formulaire', genererFormulaire($formulaire));   
}

?>
