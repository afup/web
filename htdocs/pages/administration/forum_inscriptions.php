<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer','envoyer_convocation'));
$tris_valides = array('i.date', 'i.nom', 'f.societe', 'i.etat');
$sens_valides = array( 'desc','asc' );
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

if ($action == 'envoyer_convocation')
{
    die('Désactivé pour eviter les bétises');
		$forum_inscriptions->envoyerEmailConvocation($_GET['id_forum']);
    die('ok');
}
elseif ($action == 'lister') {
    // Valeurs par défaut des paramètres de tri
    $list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription';
    $list_ordre = 'date desc';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;

    // Modification des paramètres de tri en fonction des demandes passées en GET
    if (isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides)
        && isset($_GET['sens']) && in_array($_GET['sens'], $sens_valides)) {
        $list_ordre = $_GET['tri'] . ' ' . $_GET['sens'];
    }
    if (isset($_GET['filtre'])) {
        $list_filtre = $_GET['filtre'];
    }

    if (!isset($_GET['id_forum']) || intval($_GET['id_forum']) == 0) {
        $_GET['id_forum'] = $forum->obtenirDernier();
    }
    $smarty->assign('id_forum', $_GET['id_forum']);

	// Statistiques
    $smarty->assign('forum_tarifs_lib',$AFUP_Tarifs_Forum_Lib);
    $smarty->assign('forum_tarifs',$AFUP_Tarifs_Forum);
    $smarty->assign('statistiques', $forum_inscriptions->obtenirStatistiques($_GET['id_forum']));

    // Mise en place de la liste dans le scope de smarty
    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
} elseif ($action == 'supprimer') {
    if ($forum_inscriptions->supprimerInscription($_GET['id']) && $forum_facturation->supprimerFacturation($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'inscription ' . $_GET['id']);
        afficherMessage('L\'inscription a été supprimée', 'index.php?page=forum_inscriptions&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscription', 'index.php?page=forum_inscriptions&action=lister', true);
    }
} else {
    require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Pays.php';
    $pays = new AFUP_Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
		$formulaire->setDefaults(array('civilite'            => 'M.',
									   'id_pays_facturation' => 'FR',
									   'type_inscription'    => -1,
									   'type_reglement'      => -1));
    } else {
        $champs = $forum_inscriptions->obtenir($_GET['id']);
        $champs2 = $forum_facturation->obtenir($champs['reference']);
        $champs['type_reglement']          = $champs2['type_reglement'];
        $champs['informations_reglement']  = $champs2['informations_reglement'];
        $champs['date_reglement']          = $champs2['date_reglement'];
        $champs['autorisation']            = $champs2['autorisation'];
        $champs['transaction']             = $champs2['transaction'];
        $champs['societe_facturation']     = $champs2['societe'];
        $champs['nom_facturation']         = $champs2['nom'];
        $champs['prenom_facturation']      = $champs2['prenom'];
        $champs['adresse_facturation']     = $champs2['adresse'];
        $champs['code_postal_facturation'] = $champs2['code_postal'];
        $champs['ville_facturation']       = $champs2['ville'];
        $champs['id_pays_facturation']     = $champs2['id_pays'];
        $champs['email_facturation']       = $champs2['email'];

        $formulaire->setDefaults($champs);

    	if (isset($champs) && isset($champs['id_forum'])) {
    	    $_GET['id_forum'] = $champs['id_forum'];
    	}
    }

	$formulaire->addElement('hidden', 'old_reference', (isset($champs) ? $champs['reference'] : ''));
	$formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);

	$formulaire->addElement('header', null, 'Informations');
	$groupe = array();
	foreach ($AFUP_Tarifs_Forum as $tarif_key => $tarifs)
	{
	  $groupe[] = &HTML_QuickForm::createElement('radio', 'type_inscription', null, $AFUP_Tarifs_Forum_Lib[$tarif_key] . ' (<strong>' . $AFUP_Tarifs_Forum[$tarif_key] . ' €</strong>)' , $tarif_key);
	}


	$formulaire->addGroup($groupe, 'groupe_type_inscription', 'Formule', '<br />', false);

	$formulaire->addElement('select', 'civilite'                 , 'Civilité'       , array('M.' => 'M.', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
	$formulaire->addElement('text'  , 'nom'                      , 'Nom'            , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('text'  , 'prenom'                   , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('text'  , 'email'                    , 'Email'          , array('size' => 30, 'maxlength' => 100));
	$formulaire->addElement('text'  , 'telephone'                , 'Tél.'           , array('size' => 20, 'maxlength' => 20));

	$formulaire->addElement('header', null          , 'Réservé à l\'administration');
	$formulaire->addElement('static'  , 'note'                   , ''               , 'La reference est utilisée comme numéro de facture. Elle peut être commune à plusieurs inscriptions...<br /><br />');
	$formulaire->addElement('text'  , 'reference'   , 'Référence'   , array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'autorisation', 'Autorisation', array('size' => 50, 'maxlength' => 100));
    $formulaire->addElement('text'  , 'transaction' , 'Transaction' , array('size' => 50, 'maxlength' => 100));

    $state = array(AFUP_FORUM_ETAT_CREE          => 'Inscription créée',
		AFUP_FORUM_ETAT_ANNULE            => 'Inscription annulée',
		AFUP_FORUM_ETAT_ERREUR            => 'Paiement CB erreur',
		AFUP_FORUM_ETAT_REFUSE            => 'Paiement CB refusé',
		AFUP_FORUM_ETAT_REGLE             => 'Inscription réglée',
		AFUP_FORUM_ETAT_INVITE            => 'Invitation',
		AFUP_FORUM_ETAT_ATTENTE_REGLEMENT => 'Attente règlement',
		AFUP_FORUM_ETAT_CONFIRME          => 'Inscription confirmée',
		AFUP_FORUM_ETAT_A_POSTERIORI      => 'Inscription à posteriori',
		);
	$formulaire->addElement('select', 'etat'        , 'Etat'        , $state);

    $facturation = array(AFUP_FORUM_FACTURE_A_ENVOYER => 'Facture à envoyer',
		AFUP_FORUM_FACTURE_ENVOYEE                    => 'Facture envoyée',
		AFUP_FORUM_FACTURE_RECUE                      => 'Facture reçue',
		);
	$formulaire->addElement('select', 'facturation' , 'Facturation'  , $facturation);

	$formulaire->addElement('header'  , ''                       , 'Règlement');
	$groupe = array();
	$groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Carte bancaire', AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE);
	$groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Chèque'        , AFUP_FORUM_REGLEMENT_CHEQUE);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Virement'      , AFUP_FORUM_REGLEMENT_VIREMENT);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'type_reglement', null, 'Aucun'         , AFUP_FORUM_REGLEMENT_AUCUN);
	$formulaire->addGroup($groupe, 'groupe_type_reglement', 'Règlement', '&nbsp;', false);
    $formulaire->addElement('text'   , 'informations_reglement', 'Informations règlement', array('size' => 50, 'maxlength' => 255));
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', array('language' => 'fr', 'minYear' => date('Y'), 'maxYear' => date('Y')));


	$formulaire->addElement('header'  , ''                       , 'Facturation');
	$formulaire->addElement('static'  , 'note'                   , ''               , 'Ces informations concernent la personne ou la société qui sera facturée<br /><br />');
	$formulaire->addElement('text'    , 'societe_facturation'    , 'Société'        , array('size' => 50, 'maxlength' => 100));
	$formulaire->addElement('text'    , 'nom_facturation'        , 'Nom'            , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('text'    , 'prenom_facturation'     , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
	$formulaire->addElement('textarea', 'adresse_facturation'    , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
	$formulaire->addElement('text'    , 'code_postal_facturation', 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
	$formulaire->addElement('text'    , 'ville_facturation'      , 'Ville'          , array('size' => 30, 'maxlength' => 50));
	$formulaire->addElement('select'  , 'id_pays_facturation'    , 'Pays'           , $pays->obtenirPays());
	$formulaire->addElement('text'    , 'email_facturation'      , 'Email (facture)', array('size' => 30, 'maxlength' => 100));
	$formulaire->addElement('text'    , 'coupon'                 , 'Coupon'         , array('size' => 30, 'maxlength' => 200));

	$formulaire->addElement('header', null, 'Divers');
    $formulaire->addElement('textarea', 'commentaires'           , 'Commentaires', array('cols' => 42, 'rows' => 5));
	$formulaire->addElement('static', null, null, "J'accepte que ma compagnie soit citée comme participant à la conférence");
	$groupe = array();
	$groupe[] = &HTML_QuickForm::createElement('radio', 'citer_societe', null, 'oui', 1);
	$groupe[] = &HTML_QuickForm::createElement('radio', 'citer_societe', null, 'non', 0);
	$formulaire->addGroup($groupe, 'groupe_citer_societe', null, '&nbsp;', false);
	$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant des rencontres de l'AFUP sur des sujets afférents à PHP");
	$groupe = array();
	$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_afup', null, 'oui', 1);
	$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_afup', null, 'non', 0);
	$formulaire->addGroup($groupe, 'groupe_newsletter_afup', null, '&nbsp;', false);
	$formulaire->addElement('static', null, null, "Je souhaite être tenu au courant de l'actualité PHP via la newsletter de notre sponsor");
	$groupe = array();
	$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_nexen', null, 'oui', 1);
	$groupe[] = &HTML_QuickForm::createElement('radio', 'newsletter_nexen', null, 'non', 0);
	$formulaire->addGroup($groupe, 'groupe_newsletter_nexen', null, '&nbsp;', false);

	$formulaire->addElement('header', 'boutons'  , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');

	// On ajoute les règles
	$formulaire->addGroupRule('groupe_type_inscription', 'Formule non sélectionnée' , 'required', null, 1);
	$formulaire->addGroupRule('groupe_type_reglement'  , 'Règlement non sélectionné', 'required', null, 1);
	$formulaire->addRule('civilite'               , 'Civilité non sélectionnée', 'required');
	$formulaire->addRule('nom'                    , 'Nom manquant'             , 'required');
	$formulaire->addRule('prenom'                 , 'Prénom manquant'          , 'required');
	$formulaire->addRule('email'                  , 'Email manquant'           , 'required');
	$formulaire->addRule('email'                  , 'Email invalide'           , 'email');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();

        // Date de réglement au 01/01 => non defini
        if ($valeurs['date_reglement']['d'] == 1 && $valeurs['date_reglement']['M'] == 1)
        {
            $valeurs['date_reglement'] = null;
        } else {
            $valeurs['date_reglement'] = mktime(0,0,0,$valeurs['date_reglement']['M'],$valeurs['date_reglement']['d'],$valeurs['date_reglement']['Y']);
        }

        if ($action == 'ajouter') {
			// On génére la référence si nécessaire
            if (empty($valeurs['reference'])) {
                $label = (empty($valeurs['societe_facturation']) ? (empty($valeurs['nom_facturation']) ? $valeurs['nom'] : $valeurs['nom_facturation']) : $valeurs['societe_facturation']);
    			$valeurs['reference'] = $forum_facturation->creerReference($valeurs['id_forum'], $label);
            }

			// On ajoute l'inscription dans la base de données
			$ok = $forum_inscriptions->ajouterInscription($valeurs['id_forum'],
			                                              $valeurs['reference'],
        												  $valeurs['type_inscription'],
        											 	  $valeurs['civilite'],
        												  $valeurs['nom'],
        												  $valeurs['prenom'],
        												  $valeurs['email'],
        												  $valeurs['telephone'],
        												  $valeurs['coupon'],
        												  $valeurs['citer_societe'],
        												  $valeurs['newsletter_afup'],
        		                                          $valeurs['newsletter_nexen'],
                                                          $valeurs['commentaires'],
                                                          $valeurs['etat'],
                                                          $valeurs['facturation']);
        } else {
            $ok = $forum_inscriptions->modifierInscription($_GET['id'],
            											   $valeurs['reference'],
            											   $valeurs['type_inscription'],
            											   $valeurs['civilite'],
        												   $valeurs['nom'],
        												   $valeurs['prenom'],
        												   $valeurs['email'],
        												   $valeurs['telephone'],
        												   $valeurs['coupon'],
        												   $valeurs['citer_societe'],
        												   $valeurs['newsletter_afup'],
        												   $valeurs['newsletter_nexen'],
                                                           $valeurs['commentaires'],
        												   $valeurs['etat'],
        												   $valeurs['facturation']);
        }

		$ok &= $forum_facturation->gererFacturation($valeurs['reference'],
                                                    $valeurs['type_reglement'],
                                                    $valeurs['informations_reglement'],
                                                    $valeurs['date_reglement'],
                                                    $valeurs['email_facturation'],
                                                    $valeurs['societe_facturation'],
                                                    $valeurs['nom_facturation'],
                                                    $valeurs['prenom_facturation'],
                                                    $valeurs['adresse_facturation'],
                                                    $valeurs['code_postal_facturation'],
                                                    $valeurs['ville_facturation'],
                                                    $valeurs['id_pays_facturation'],
                                                    $valeurs['id_forum'],
                                                    $valeurs['old_reference'],
                                                    $valeurs['autorisation'],
                                                    $valeurs['transaction'],
                                                    $valeurs['etat']);

        if ($ok) {
            if ($action == 'ajouter') {
                AFUP_Logs::log('Ajout de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                AFUP_Logs::log('Modification de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
            }
            afficherMessage('L\'inscription a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=forum_inscriptions&action=lister');
        } else {
            $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de l\'inscription');
        }
    }

    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));
}

?>