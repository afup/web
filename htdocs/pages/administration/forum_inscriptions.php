<?php

$action = verifierAction(array('lister', 'ajouter', 'modifier', 'supprimer','envoyer_convocation', 'generer_mail_inscription_afup', 'generer_inscription_afup'));
$tris_valides = array('i.date', 'i.nom', 'f.societe', 'i.etat');
$sens_valides = array( 'desc','asc' );
$smarty->assign('action', $action);

require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Facturation_Forum.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Forum.php';

$forum = new AFUP_Forum($bdd);
$forum_inscriptions = new AFUP_Inscriptions_Forum($bdd);
$forum_facturation = new AFUP_Facturation_Forum($bdd);

if ($action == 'envoyer_convocation') {
	$formulaire = &instancierFormulaire();
	$formulaire->setDefaults(array('sujet' => 'Convocation pour le Forum PHP 2014',
                                'date_envoi' => date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, date('Y'))),
								'corps' => 'Bonjour %INSCRIT,

Le Forum PHP 2014 est à nos portes ! Et l\'événement annonçant complet, atteignant un niveau d\'inscriptions quasiment historique pour l\'AFUP, il promet d\'être mémorable... 
Voici les dernières informations utiles à connaître afin de nous rejoindre :

 - Quand et comment retirer votre badge

Le Forum PHP 2014 se tiendra au Beffroi de Montrouge, les jeudi 23 et vendredi 24 octobre 2014. Présentez-vous à l\'accueil du Forum PHP 2014 afin de retirer votre badge auprès de notre équipe. Préservons l\'environnement, l\'impression de cette convocation (%LIEN%) n\'est pas nécessaire !
Les portes ouvriront à 8h30. Les premières conférences débutent le jeudi à 9h : nous vous encourageons à être à l\'heure, afin d\'avoir le temps de récupérer votre badge, déposer vos affaires au vestiaire et profiter du buffet de petit-déjeuner. Ne vous préoccupez pas du déjeuner : votre billet d\'entrée vous permettra de profiter d\'un en-cas à la pause du midi.


 - Comment se rendre au Forum PHP 2014 ?

Le Beffroi de Montrouge se trouve à la sortie du métro "Mairie de Montrouge", ligne 4. Il est situé place Emile Cresp, à Montrouge. Le grand bâtiment, en briques rouges, est immanquable !


 - Les ateliers pratiques, grande nouveauté du Forum PHP 2014

Cette année nous mettons en place 4 ateliers pratiques : en petit comité, profitez des conseils des meilleurs experts sur un sujet précis, le tout pendant une demi-journée en direct sur votre machine. Ces ateliers se déroulent uniquement sur inscription. Vous avez pris votre place ? Il vous suffira de vous présenter avec votre coupon EventBrite?, à l\'entrée de la salle réservée aux ateliers. Vous êtes intéressé par un atelier ? Il reste quelques places pour l\'atelier "Chasse aux bugs" mené par Sophie Beaupuis le vendredi après-midi (http://bit.ly/ZS2F3h)


 - Profitez du Forum PHP 2014 pour consulter nos docteur ès PHP au sein des cliniques-conseils !

Nos sponsors font venir leurs meilleurs experts pour soigner tous vos bobos PHP. Ainsi, Microsoft et Alterway, sponsors Platine de notre événement, vous proposeront deux cliniques-conseil, intitulées "Tirer le meilleur parti du Cloud avec Microsoft Azure et PHP, venez déployer votre projet !" dont se chargera Benjamin Moulès de Microsoft et Brainsonix, et "Adopter une démarche DevOps? pour les applications PHP déployées dans le Cloud Microsoft Azure", proposée par Stéphane Goudeau de Microsoft et Hervé Leclerc d’AlterWay?. Théodo, sponsor Or, proposera une clinique-conseil Théodo "Premiers secours", pour parer à tous vos petits soucis PHP et remplir votre trousse de secours de tous les indispensables : devops, travis, le débogage, le service en TDD ou encore l\'agilité / lean. Blablacar, sponsor Or, vous proposera de rencontrer Olivier Dolbeau sur la thématique qu\'il abordera durant sa conférence "Laisse pas trainer ton log !" programmée le jeudi 23 octobre de 12h15 à 13h. Enfin, Zend et Vesperia proposeront en commun une clinique-conseil intitulée "Continuous Deployment", pour vous emmener au-delà du DevOps? !


 - Le plus grand apéro communautaire parisien de l\'année !

Le jeudi 23 octobre, rendez-vous dès la fin des conférences au café Oz, place Denfert Rochereau. Un apéro dînatoire vous y attendra, et toute l\'équipe d\'organisation, les conférenciers et les sponsors seront présents pour ce qui s\'annonce comme la plus grande soirée communautaire de cette fin d\'année !


 - Le Forum PHP 2014 sur les réseaux sociaux et sur votre mobile

Le programme des 2 jours est disponible en version mobile : http://m.afup.org
Envie de tweeter pendant le Forum PHP 2014 pour en faire profiter vos followers ? Utilisez le hashtag #forumphp ! Et n\'oubliez pas de suivre l\'actualité de l\'AFUP sur nos réseaux sociaux :
Twitter : @afup 
Facebook : www.facebook.com/fandelafup
Google + : https://plus.google.com/u/0/b/103588986855606151405/103588986855606151405/posts

Nous avons hâte de vous accueillir !

A bientôt,
L\'équipe AFUP'));

	$formulaire->addElement('hidden', 'id_forum', $_GET['id_forum']);
	$formulaire->addElement('hidden', 'action', 'envoyer_convocation');
	$formulaire->addElement('header', null, 'Convocation');
    $formulaire->addElement('text', 'sujet', 'Sujet', array('size' => 30));
    $formulaire->addElement('text', 'date_envoi', 'Date envoi');
	$formulaire->addElement('textarea', 'corps', 'Corps', array('cols' => 60, 'rows' => 20));
	$formulaire->addElement('header', 'boutons' , '');
	$formulaire->addElement('submit', 'soumettre', 'Soumettre');

	$formulaire->addRule('sujet', 'Sujet manquant', 'required');
    $formulaire->addRule('corps', 'Corps manquant', 'required');
	$formulaire->addRule('date_envoi', 'Date manquante', 'required');

    if ($formulaire->validate()) {
		$valeurs = $formulaire->exportValues();
		$resultat = $forum_inscriptions->envoyerEmailConvocation($valeurs['id_forum'], $valeurs['sujet'], $valeurs['corps'], $valeurs['date_envoi']);
		if ($resultat) {
			AFUP_Logs::log('Envoi de la convocation pour le Forum PHP');
			afficherMessage('La convocation a été envoyée', 'index.php?page=forum_inscriptions&action=lister');
		} else {
			AFUP_Logs::log('Echec de l\'envoi de la convocation pour le Forum PHP');
			afficherMessage('L\'envoi de la convocation a échouée', 'index.php?page=forum_inscriptions&action=lister', true);
		}
    }
    $current = $forum->obtenir($_GET['id_forum'], 'titre');
    $smarty->assign('forum_name', $current['titre']);
    $smarty->assign('formulaire', genererFormulaire($formulaire));

} elseif ($action == 'lister') {
    $list_champs = 'i.id, i.date, i.nom, i.prenom, i.email, f.societe, i.etat, i.coupon, i.type_inscription, i.mobilite_reduite, f.type_reglement';
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
    $smarty->assign('id_forum', $_GET['id_forum']);

    $smarty->assign('forum_tarifs_lib',$AFUP_Tarifs_Forum_Lib);
    $smarty->assign('forum_tarifs',$AFUP_Tarifs_Forum);
    $smarty->assign('statistiques', $forum_inscriptions->obtenirStatistiques($_GET['id_forum']));

    $smarty->assign('forums', $forum->obtenirListe());
    $smarty->assign('inscriptions', $forum_inscriptions->obtenirListe($_GET['id_forum'], $list_champs, $list_ordre, $list_associatif, $list_filtre));

} elseif ($action == 'supprimer') {
    if ($forum_inscriptions->supprimerInscription($_GET['id']) && $forum_facturation->supprimerFacturation($_GET['id'])) {
        AFUP_Logs::log('Suppression de l\'inscription ' . $_GET['id']);
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
    $formulaire->addElement('date'    , 'date_reglement'     , 'Date', array('language' => 'fr', 'minYear' => 2002, 'maxYear' => date('Y') + 5));


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
