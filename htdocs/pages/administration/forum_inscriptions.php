<?php

// Impossible to access the file itself
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}
/**
 * @var $this \AppBundle\Controller\LegacyController
 */

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer','envoyer_convocation', 'generer_mail_inscription_afup', 'generer_inscription_afup'));
$tris_valides = array('i.date', 'i.nom', 'f.societe', 'i.etat');
$sens_valides = array( 'desc','asc' );
$smarty->assign('action', $action);

$eventRepository = $this->get(\AppBundle\Event\Model\Repository\EventRepository::class);
$ticketEventTypeRepository = $this->get(\AppBundle\Event\Model\Repository\TicketEventTypeRepository::class);

function updateGlobalsForTarif(
    \AppBundle\Event\Model\Repository\EventRepository $eventRepository,
    \AppBundle\Event\Model\Repository\TicketEventTypeRepository $ticketEventTypeRepository,
    $forumId,
    &$membersTickets = []
) {
    global $AFUP_Tarifs_Forum, $AFUP_Tarifs_Forum_Lib;
    $event = $eventRepository->get($forumId);
    $ticketTypes = $ticketEventTypeRepository->getTicketsByEvent($event, false);

    foreach ($ticketTypes as $ticketType) {
        /**
         * @var $ticketType \AppBundle\Event\Model\TicketEventType
         */
        $AFUP_Tarifs_Forum[$ticketType->getTicketTypeId()] = $ticketType->getPrice();
        $AFUP_Tarifs_Forum_Lib[$ticketType->getTicketTypeId()] = $ticketType->getTicketType()->getPrettyName();

        if ($ticketType->getTicketType()->getIsRestrictedToMembers()) {
            $membersTickets[] = $ticketType->getTicketTypeId();
        }
    }
}



$forum = new Forum($bdd);
$forum_inscriptions = new Inscriptions($bdd);
$forum_facturation = new Facturation($bdd);

if ($action == 'envoyer_convocation') {
    $current = $forum->obtenir($_GET['id_forum'], 'titre');

	$formulaire = &instancierFormulaire();
    $formulaire->setDefaults(array('template' => 'convocation-???'));

	$formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);
	$formulaire->addElement('hidden', 'action', 'envoyer_convocation');
	$formulaire->addElement('header', null, 'Convocation (seulement à ceux qui doivent la recevoir, aucun conférencier)');
    $formulaire->addElement('text', 'template', 'Template Mandrill', array('size' => 255));
	$formulaire->addElement('header', 'boutons' , '');
	$formulaire->addElement('submit', 'soumettre', 'Envoyer la convoc Saperlipopette !');

	$formulaire->addRule('template', 'Template manquant', 'required');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();
		$resultat = $forum_inscriptions->envoyerEmailConvocation($valeurs['id_forum'], $valeurs['template']);
		if ($resultat) {
			Logs::log("Envoi de la convocation pour le {$current['titre']}");
			afficherMessage('La convocation a été envoyée', 'index.php?page=forum_inscriptions&action=lister');
		} else {
			Logs::log("Echec de l'envoi de la convocation pour le {$current['titre']}");
			afficherMessage('L\'envoi de la convocation a échouée', 'index.php?page=forum_inscriptions&action=lister', true);
		}
    }
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'lister') {
    $list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription, i.mobilite_reduite, f.type_reglement, i.presence_day1, i.presence_day2';
    $list_ordre = 'date desc';
    $list_sens = 'desc';
    $list_associatif = false;
    $list_filtre = false;

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
    $forumData = $forum->obtenir($_GET['id_forum']);
    $smarty->assign('id_forum', $_GET['id_forum']);
    $memberTickets = [];

    updateGlobalsForTarif($eventRepository, $ticketEventTypeRepository, $_GET['id_forum'], $memberTickets);

    $smarty->assign('forum_tarifs_members', $memberTickets);
    $smarty->assign('forum_tarifs_lib',$AFUP_Tarifs_Forum_Lib);
    $smarty->assign('forum_tarifs',$AFUP_Tarifs_Forum);
    $smarty->assign('statistiques', $forum_inscriptions->obtenirStatistiques($_GET['id_forum']));

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));
    $smarty->assign('finForum', (new \DateTime($forumData['date_fin']))->format('U'));
    $smarty->assign('now', (new \DateTime())->format('U'));

} elseif ($action == 'supprimer') {
    if ($forum_inscriptions->supprimerInscription($_GET['id']) && $forum_facturation->supprimerFacturation($_GET['id'])) {
        Logs::log('Suppression de l\'inscription ' . $_GET['id']);
        afficherMessage('L\'inscription a été supprimée', 'index.php?page=forum_inscriptions&action=lister');
    } else {
        afficherMessage('Une erreur est survenue lors de la suppression de l\'inscription', 'index.php?page=forum_inscriptions&action=lister', true);
    }
} elseif ($action == 'generer_mail_inscription_afup') {
    $champs = $forum_inscriptions->obtenir($_GET['id']);
    $champs2 = $forum_facturation->obtenir($champs['reference']);
    $info_forum = $forum->obtenir($champs['id_forum']);
    $texte  = ' - civilité :    ' . $champs['civilite'] . PHP_EOL;
    $texte .= ' - nom :         ' . $champs['nom'] . PHP_EOL;
    $texte .= ' - prénom :      ' . $champs['prenom'] . PHP_EOL;
    $texte .= ' - email :       ' . $champs['email'] . PHP_EOL;
    $texte .= ' - adresse :     ' . $champs2['adresse'] . PHP_EOL;
    $texte .= ' - code postal : ' . $champs2['code_postal'] . PHP_EOL;
    $texte .= ' - ville :       ' . $champs2['ville'] . PHP_EOL;
    $texte .= ' - pays :        ' . $champs2['id_pays'] . PHP_EOL;
    $smarty->assign('texte_mail', $texte);
    $smarty->assign('info_forum', $info_forum);
} elseif ($action == 'generer_inscription_afup') {
    $champs = $forum_inscriptions->obtenir($_GET['id']);
    $champs2 = $forum_facturation->obtenir($champs['reference']);
    $_SESSION['generer_personne_physique']['civilite'] = $champs['civilite'];
    $_SESSION['generer_personne_physique']['nom'] = $champs['nom'];
    $_SESSION['generer_personne_physique']['prenom'] = $champs['prenom'];
    $_SESSION['generer_personne_physique']['email'] = $champs['email'];
    $_SESSION['generer_personne_physique']['adresse'] = $champs2['adresse'];
    $_SESSION['generer_personne_physique']['code_postal'] = $champs2['code_postal'];
    $_SESSION['generer_personne_physique']['ville'] = $champs2['ville'];
    $_SESSION['generer_personne_physique']['id_pays'] = $champs2['id_pays'];
    $_SESSION['generer_personne_physique']['telephone_fixe'] = $champs['telephone'];
    $_SESSION['generer_personne_physique']['telephone_portable'] = $champs['telephone'];
    $_SESSION['generer_personne_physique']['etat'] = 1;
    afficherMessage("L'inscription a été pré-remplie\nPensez à générer le login", 'index.php?page=personnes_physiques&action=ajouter');
} else {

    $pays = new Pays($bdd);

    $formulaire = &instancierFormulaire();
    if ($action == 'ajouter') {
		$formulaire->setDefaults(
		    [
                'civilite' => 'M.',
                'id_pays_facturation' => 'FR',
                'type_inscription' => -1,
                'type_reglement' => -1,
                'citer_societe' => 1,
                'mail_partenaire' => 0,
                'newsletter_afup' => 0,
                'newsletter_nexen' => 0,
                'mobilite_reduite' => 0,
                'date_reglement' => (new \DateTime())->format('Y-m-d')
            ]
        );
    } else {
        $champs = $forum_inscriptions->obtenir($_GET['id']);
        if ($champs == false) {
            afficherMessage('L\'inscription n\'existe plus', 'index.php?page=forum_inscriptions&action=lister');
            exit(0);
        }
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
    updateGlobalsForTarif($eventRepository, $ticketEventTypeRepository, $_GET['id_forum']);

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

    $groupe = array();
    $groupe[] = &HTML_QuickForm::createElement('radio', 'mobilite_reduite', null, 'oui', 1);
    $groupe[] = &HTML_QuickForm::createElement('radio', 'mobilite_reduite', null, 'non', 0);
    $formulaire->addGroup($groupe, 'groupe_mobilite_reduite', 'Personne à mobilité réduite', '<br/>', false);

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
    $formulaire->addElement('textarea'   , 'informations_reglement', 'Informations règlement', array('cols' => 42, 'rows' => 4));


    $current = $forum->obtenir($_GET['id_forum']);
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', array('language' => 'fr', 'minYear' => $current['forum_annee']-2, 'maxYear' => $current['forum_annee']+2));


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
    $formulaire->addElement('static', null, null, "Je souhaite recevoir des informations de la part de vos partenaires presse/media");
	$groupe = array();
	$groupe[] = &HTML_QuickForm::createElement('radio', 'mail_partenaire', null, 'oui', 1);
	$groupe[] = &HTML_QuickForm::createElement('radio', 'mail_partenaire', null, 'non', 0);
	$formulaire->addGroup($groupe, 'groupe_mail_partenaire', null, '&nbsp;', false);

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
                                                          $valeurs['mobilite_reduite'],
                                                          $valeurs['mail_partenaire'],
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
        												   $valeurs['mail_partenaire'],
                                                           $valeurs['commentaires'],
        												   $valeurs['etat'],
        												   $valeurs['facturation'],
                                                           $valeurs['mobilite_reduite']);
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
                Logs::log('Ajout de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom'));
            } else {
                Logs::log('Modification de l\'inscription de ' . $formulaire->exportValue('prenom') . ' ' . $formulaire->exportValue('nom') . ' (' . $_GET['id'] . ')');
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
