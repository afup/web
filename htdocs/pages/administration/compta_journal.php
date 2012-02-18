<?php

$action = verifierAction(array('lister', 'debit','credit','ajouter', 'modifier','supprimer', 'importer', 'ventiler'));
//$tris_valides = array('Date', 'Evenement', 'catégorie', 'Description');
//$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Compta.php';
$compta = new AFUP_Compta($bdd);


if (isset($_GET['id_periode']) && $_GET['id_periode']) {
	$id_periode=$_GET['id_periode'];
} else {
	$id_periode="";
}

$id_periode = $compta->obtenirPeriodeEnCours($id_periode);
$smarty->assign('id_periode', $id_periode);

$listPeriode = $compta->obtenirListPeriode();
$smarty->assign('listPeriode', $listPeriode );


	$periode_debut=$listPeriode[$id_periode-1]['date_debut'];
	$periode_fin=$listPeriode[$id_periode-1]['date_fin'];

if ($action == 'lister') {
	$journal = $compta->obtenirJournal('',$periode_debut,$periode_fin);
	$smarty->assign('journal', $journal);
}
elseif ($action == 'debit') {
	$journal = $compta->obtenirJournal(1,$periode_debut,$periode_fin);
	$smarty->assign('journal', $journal);
}
elseif ($action == 'credit') {
	$journal = $compta->obtenirJournal(2,$periode_debut,$periode_fin);
	$smarty->assign('journal', $journal);

} elseif ($action == 'ajouter' || $action == 'modifier') {

  	$formulaire = &instancierFormulaire();

   if ($action == 'modifier')
   {
        $champsRecup = $compta->obtenir($_GET['id']);

        $champs['date_saisie']          = $champsRecup['date_ecriture'];
        $champs['idoperation']          = $champsRecup['idoperation'];
        $champs['idcategorie']          = $champsRecup['idcategorie'];
        $champs['nom_frs']          = $champsRecup['nom_frs'];
        $champs['montant']          = $champsRecup['montant'];
        $champs['description']          = $champsRecup['description'];
        $champs['numero']          = $champsRecup['numero'];
        $champs['idmode_regl']          = $champsRecup['idmode_regl'];
        $champs['date_reglement']          = $champsRecup['date_regl'];
        $champs['obs_regl']          = $champsRecup['obs_regl'];
        $champs['idevenement']          = $champsRecup['idevenement'];

		$formulaire->setDefaults($champs);
		//$formulaire->setDefaults($champsRecup);
		$formulaire->addElement('hidden', 'id', $_GET['id']);
   }

// facture associé à un évènement
   $formulaire->addElement('header'  , ''                         , 'Sélectionner un Journal');
   $formulaire->addElement('select'  , 'idoperation', 'Type d\'opération', $compta->obtenirListOperations());
   $formulaire->addElement('select'  , 'idevenement', 'Evenement', $compta->obtenirListEvenements());

//detail facture
   $formulaire->addElement('header'  , ''                         , 'Détail Facture');

//$mois=10;
   $formulaire->addElement('date'    , 'date_saisie'     , 'Date saisie', array('language' => 'fr',
                                                                                'format'   => 'd F Y',
  																				'minYear' => date('Y')-5,
  																				'maxYear' => date('Y')+1));

  $formulaire->addElement('select'  , 'idcategorie', 'Type de compte', $compta->obtenirListCategories());
  $formulaire->addElement('text', 'nom_frs', 'Nom fournisseurs' , array('size' => 30, 'maxlength' => 40));
   	$formulaire->addElement('text', 'numero', 'Numero facture' , array('size' => 30, 'maxlength' => 40));
   	$formulaire->addElement('textarea', 'description', 'Description', array('cols' => 42, 'rows' => 5));
	$formulaire->addElement('text', 'montant', 'Montant' , array('size' => 30, 'maxlength' => 40));

//reglement
   $formulaire->addElement('header'  , ''                         , 'Réglement');
   $formulaire->addElement('select'  , 'idmode_regl', 'Réglement', $compta->obtenirListReglements());
   $formulaire->addElement('date'    , 'date_reglement'     , 'Date', array('language' => 'fr',
                                                                            'format'   => 'd F Y',
   																			'minYear' => date('Y')-5,
   																			'maxYear' => date('Y')+1));
   $formulaire->addElement('text', 'obs_regl', 'Info reglement' , array('size' => 30, 'maxlength' => 40));


// boutons
    $formulaire->addElement('header'  , 'boutons'                  , '');
    $formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

	// 2012-02-18 A. Gendre
	$passer = null;
	if($action != 'ajouter'){
		$res = $compta->obtenirSuivantADeterminer($_GET['id']);
		if(is_array($res)){
			$passer = $res['id'];
			$formulaire->addElement('submit', 'soumettrepasser'   , 'Soumettre & passer');
			$formulaire->addElement('submit', 'passer'   , 'Passer');
			echo "###".$passer;
		}
	}	

	// ajoute des regles
	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'required');
	$formulaire->addRule('idoperation'   , 'Type d\'opération manquant'    , 'nonzero');
	$formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'required');
	$formulaire->addRule('idevenement'    , 'Evenement manquant'   , 'nonzero');
	$formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'required');
	$formulaire->addRule('idcategorie'    , 'Type de compte manquant'     , 'nonzero');
	$formulaire->addRule('montant'       , 'Montant manquant'      , 'required');

	
	// 2012-02-18 A. Gendre
	if (isset($_POST['passer']) && isset($passer)) {
		 afficherMessage('L\'écriture n\'a pas été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=compta_journal&action=modifier&id=' . $passer);
		 return;
	}

    if ($formulaire->validate()) {
		$valeur = $formulaire->exportValues();

$date_ecriture= $valeur['date_saisie']['Y']."-".$valeur['date_saisie']['F']."-".$valeur['date_saisie']['d'] ;
$date_regl=$valeur['date_reglement']['Y']."-".$valeur['date_reglement']['F']."-".$valeur['date_reglement']['d'] ;

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
                AFUP_Logs::log('Ajout une écriture ' . $formulaire->exportValue('titre'));
            } else {
                AFUP_Logs::log('Modification une écriture ' . $formulaire->exportValue('titre') . ' (' . $_GET['id'] . ')');
            }
			// 2012-02-18 A. Gendre
			if (isset($_POST['soumettrepasser']) && isset($passer)) {
				$urlredirect = 'index.php?page=compta_journal&action=modifier&id=' . $passer;
			} else {
				$urlredirect = 'index.php?page=compta_journal&action=lister#L' . $valeur['id'];
			} 
			afficherMessage('L\'écriture a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), $urlredirect);
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'écriture');
        }
    }


    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'supprimer') {
    if ($compta->supprimerEcriture($_GET['id']) ) {
        AFUP_Logs::log('Suppression de l\'écriture ' . $_GET['id']);
        afficherMessage('L\'écriture a été supprimée', 'index.php?page=compta_journal&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'écriture', 'index.php?page=compta_journal&action=lister', true);
    }
} elseif ($action == 'importer') {
    $formulaire = &instancierFormulaire();
	$formulaire->addElement('header', null          , 'Import CSV');
    $formulaire->addElement('file', 'fichiercsv', 'Fichier banque'     );

	$formulaire->addElement('header', 'boutons'  , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();
        $file =& $formulaire->getElement('fichiercsv');
        $tmpDir = dirname(__FILE__) . '/../../../tmp';
        if ($file->isUploadedFile()) {
            $file->moveUploadedFile($tmpDir, 'banque.csv');
            $lignes = file($tmpDir . '/banque.csv');
            if ($compta->extraireComptaDepuisCSVBanque($lignes)) {
                AFUP_Logs::log('Chargement fichier banque');
                afficherMessage('Le fichier a été importé', 'index.php?page=compta_journal&action=lister');
            } else {
                afficherMessage('Le fichier n\'a pas été importé', 'index.php?page=compta_journal&action=lister');
            }
            unlink($tmpDir . '/banque.csv');
        }
    }
    $smarty->assign('formulaire', genererFormulaire($formulaire));
} elseif ($action == 'ventiler') {
    $idCompta = (int)$_GET['id'];
    $montant = (float) $_GET['montant'];
    $ligneCompta = $compta->obtenir($idCompta);
    $compta->ajouter($ligneCompta['idoperation'],
                     26, // A déterminer
                     $ligneCompta['date_ecriture'],
                     $ligneCompta['nom_frs'],
                     $montant,
                     $ligneCompta['description'],
                     $ligneCompta['numero'],
                     $ligneCompta['idmode_regl'],
                     $ligneCompta['date_regl'],
                     $ligneCompta['obs_regl'],
                     8, // A déterminer
                     $ligneCompta['numero_operation']);
    $compta->modifier($ligneCompta['id'],
                      $ligneCompta['idoperation'],
                      $ligneCompta['idcategorie'],
                      $ligneCompta['date_ecriture'],
                      $ligneCompta['nom_frs'],
                      $ligneCompta['montant'] - $montant,
                      $ligneCompta['description'],
                      $ligneCompta['numero'],
                      $ligneCompta['idmode_regl'],
                      $ligneCompta['date_regl'],
                      $ligneCompta['obs_regl'],
                      $ligneCompta['idevenement'],
                      $ligneCompta['numero_operation']);
    afficherMessage('L\'écriture a été ventilée', 'index.php?page=compta_journal&action=modifier&id=' . $compta->lastId);
}