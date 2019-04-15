<?php

// Impossible to access the file itself
use Afup\Site\AFUP_AnnuairePro_Membres;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
	trigger_error("Direct access forbidden.", E_USER_ERROR);
	exit;
}

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer', 'nettoyer'));
$tris_valides = array('RaisonSociale', 'SIREN', 'SiteWeb');
$sens_valides = array('asc', 'desc');
$smarty->assign('action', $action);

$annuairepro_membres = new AFUP_AnnuairePro_Membres($bdd);

if ($action == 'lister') {

		// Valeurs par dfaut des paramtres de tri
		$list_champs = '*';
		$list_ordre = 'DateCreation DESC';
		$list_sens = 'asc';
		$list_associatif = false;
		$list_filtre = false;

		// Modification des paramtres de tri en fonction des demandes passes en GET
		if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
				&& isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
				$list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
		}

		// Mise en place de la liste dans le scope de smarty
		$membres = $annuairepro_membres->obtenirListe($list_champs, $list_ordre, $list_associatif, $list_filtre);
		$membres = $annuairepro_membres->retoucherListe($membres);
		$smarty->assign('membres', $membres);

} elseif ($action == 'supprimer') {
		if ($annuairepro_membres->supprimer($_GET['id'])) {
				Logs::log('Suppression du membre de l\'annuaire pro ' . $_GET['id']);
				afficherMessage('Le membre de l\'annuaire pro a été supprimé', 'index.php?page=annuairepro_membres&action=lister');
		} else {
				afficherMessage('Une erreur est survenue lors de la suppression du membre de l\'annuaire pro', 'index.php?page=annuairepro_membres&action=lister', true);
		}

} elseif ($action == 'nettoyer') {
		if ($annuairepro_membres->nettoyer()) {
				Logs::log('Suppression des membres spammeurs de l\'annuaire pro');
				afficherMessage('Les membres spammeurs de l\'annuaire pro ont été supprimé', 'index.php?page=annuairepro_membres&action=lister');
		} else {
				afficherMessage('Une erreur est survenue lors de la suppression des membres spammeurs de l\'annuaire pro', 'index.php?page=annuairepro_membres&action=lister', true);
		}

} else {
		$pays = new Pays($bdd);

		$formulaire = instancierFormulaire();
		if ($action == 'ajouter') {
				$formulaire->setDefaults(array('id_pays' => 'FR',
																			'SIREN-Test'    => 'KO',
																			'etat'    => AFUP_DROITS_ETAT_ACTIF));
		$champs['SiteWeb'] = '';
				$champs['SIREN-Test'] = 'KO';
		} else {
				$champs = $annuairepro_membres->obtenir($_GET['id']);
				$champs['SIREN-Test'] = $annuairepro_membres->VerifierSIREN($champs['SIREN']);
				$formulaire->setDefaults($champs);
		}


		$formulaire->addElement('header'  , ''                   , 'Informations');
		$formulaire->addElement('text'    , 'RaisonSociale'      , 'Raison sociale'       , array('size' => 30, 'maxlength' => 40));
		$formulaire->addElement('select'  , 'FormeJuridique'     , 'Forme juridique'      , $annuairepro_membres->obtenirFormesJuridiques());
		$formulaire->addElement('text'    , 'SIREN'              , 'SIREN'                , array('size' => 30, 'maxlength' => 40));
		$formulaire->addElement('text'    , 'SIREN-Test'         , ''                     , array('size' => 3, 'disabled' => 'disabled', 'class' => $champs['SIREN-Test']));
		$formulaire->addElement('text'    , 'NumeroFormateur'    , 'Numéro formateur'     , array('size' => 20, 'maxlength' => 20));
		$formulaire->addElement('text'    , 'MembreAFUP'         , 'Membre AFUP'          , array('size' => 20, 'maxlength' => 20));
		$formulaire->addElement('text'    , 'DateCreation'       , 'Date de cration'      , array('size' => 20, 'maxlength' => 20));
		$formulaire->addElement('select'  , 'TailleSociete'      , 'Taille de la société' , $annuairepro_membres->obtenirTaillesEntreprise());

		$formulaire->addElement('header'  , ''                   , 'Contacts');
		$formulaire->addElement('textarea', 'Adresse'            , 'Adresse'              , array('cols' => 42, 'rows'      => 10));
		$formulaire->addElement('text'    , 'CodePostal'         , 'Code postal'          , array('size' =>  6, 'maxlength' => 10));
		$formulaire->addElement('text'    , 'Ville'              , 'Ville'                , array('size' => 30, 'maxlength' => 50));
		$formulaire->addElement('select'  , 'Zone'               , 'Zone'                 , $pays->obtenirZonesFrancaises());
		$formulaire->addElement('select'  , 'id_pays'            , 'Pays'                 , $pays->obtenirPays());
		$formulaire->addElement('text'    , 'Email'              , 'Email'                , array('size' => 30, 'maxlength' => 100));
		$formulaire->addElement('text'    , 'SiteWeb'            , 'Site Web'             ,$annuairepro_membres->retoucherSiteWeb($champs['SiteWeb'], 'Site web') , array('size' => 30, 'maxlength' => 100));
		$formulaire->addElement('text'    , 'Telephone'          , 'Tél.'                 , array('size' => 20, 'maxlength' => 20));
		$formulaire->addElement('text'    , 'Fax'                , 'Fax'                  , array('size' => 20, 'maxlength' => 20));


		$formulaire->addElement('header'  , ''                   , 'Paramètres');
		$formulaire->addElement('select'  , 'Valide'             , 'Etat'                 , array(AFUP_ANNUAIRE_ETAT_ACTIF   => 'Actif',
																																															AFUP_ANNUAIRE_ETAT_INACTIF => 'Inactif'));
		$formulaire->addElement('text'    , 'Password'           , 'Mot de passe'         , array('size' => 20, 'maxlength' => 20));

		$formulaire->addElement('header'  , 'boutons'            , '');
		$formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

/*    $formulaire->addRule('nom'         , 'Nom manquant'         , 'required');
		$formulaire->addRule('prenom'      , 'Prnom manquant'      , 'required');
		$formulaire->addRule('email'       , 'Email manquant'       , 'required');
		$formulaire->addRule('email'       , 'Email invalide'       , 'email');
		$formulaire->addRule('raison_sociale', 'Raison sociale manquante', 'required');
		$formulaire->addRule('adresse'       , 'Adresse manquante'       , 'required');
		$formulaire->addRule('code_postal'   , 'Code postal manquant'    , 'required');
		$formulaire->addRule('ville'         , 'Ville manquante'         , 'required');*/

		if ($formulaire->validate()) {
				if ($action == 'ajouter') {
						$ok = $annuairepro_membres->ajouter($formulaire->exportValue('FormeJuridique'),
																								$formulaire->exportValue('RaisonSociale'),
																								$formulaire->exportValue('SIREN'),
																								$formulaire->exportValue('Email'),
																								$formulaire->exportValue('SiteWeb'),
																								$formulaire->exportValue('Telephone'),
																								$formulaire->exportValue('Fax'),
																								$formulaire->exportValue('Adresse'),
																								$formulaire->exportValue('CodePostal'),
																								$formulaire->exportValue('Ville'),
																								$formulaire->exportValue('Zone'),
																								$formulaire->exportValue('id_pays'),
																								$formulaire->exportValue('NumeroFormateur'),
																								$formulaire->exportValue('MembreAFUP'),
																								$formulaire->exportValue('Valide'),
																								$formulaire->exportValue('DateCreation'),
																								$formulaire->exportValue('TailleSociete'),
																								$formulaire->exportValue('Password'));
				} else {
						$ok = $annuairepro_membres->modifier($_GET['id'],
																								$formulaire->exportValue('FormeJuridique'),
																								$formulaire->exportValue('RaisonSociale'),
																								$formulaire->exportValue('SIREN'),
																								$formulaire->exportValue('Email'),
																								$formulaire->exportValue('SiteWeb'),
																								$formulaire->exportValue('Telephone'),
																								$formulaire->exportValue('Fax'),
																								$formulaire->exportValue('Adresse'),
																								$formulaire->exportValue('CodePostal'),
																								$formulaire->exportValue('Ville'),
																								$formulaire->exportValue('Zone'),
																								$formulaire->exportValue('id_pays'),
																								$formulaire->exportValue('NumeroFormateur'),
																								$formulaire->exportValue('MembreAFUP'),
																								$formulaire->exportValue('Valide'),
																								$formulaire->exportValue('DateCreation'),
																								$formulaire->exportValue('TailleSociete'),
																								$formulaire->exportValue('Password'));
				}

				if ($ok) {
						if ($action == 'ajouter') {
								Logs::log('Ajout du membre de l\'annuaire pro ' . $formulaire->exportValue('RaisonSociale'));
						} else {
								Logs::log('Modification du membre de l\'annuaire pro ' . $formulaire->exportValue('RaisonSociale') . ' (' . $_GET['id'] . ')');
						}
						afficherMessage('Le membre de l\'annuaire pro a été ' . (($action == 'ajouter') ? 'ajouté' : 'modifié'), 'index.php?page=annuairepro_membres&action=lister');
				} else {
						$smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' du membre de l\'annuaire pro');
				}
		}

		$smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>
