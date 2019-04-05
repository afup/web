<?php

// Impossible to access the file itself
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$formulaire = instancierFormulaire();
$defaults = $conf->exporter();
if ($defaults['paybox_prod|site'] == $defaults['paybox|site'] &&
    $defaults['paybox_prod|rang'] == $defaults['paybox|rang']) {
	$type = 'prod';
} else {
	$type = 'test';
}
$defaults['paybox'] =  $type;
unset($defaults['paybox|site']);
unset($defaults['paybox|rang']);
$formulaire->setDefaults($defaults);

$formulaire->addElement('header', ''                , 'Base de donnes');
$formulaire->addElement('text'  , 'bdd|hote'        , 'Hote'           , array('size' => 30));
$formulaire->addElement('text'  , 'bdd|base'        , 'Base'           , array('size' => 30));
$formulaire->addElement('text'  , 'bdd|utilisateur' , 'Utilisateur'    , array('size' => 30));
$formulaire->addElement('text'  , 'bdd|mot_de_passe', 'Mot de passe'   , array('size' => 30));

$formulaire->addElement('header'  , ''                                      , 'Mails');
$formulaire->addElement('text'    , 'mails|email_expediteur'                , 'Email expediteur'                , array('size' => 30));
$formulaire->addElement('text'    , 'mails|nom_expediteur'                  , 'Nom expediteur'                  , array('size' => 30));
$formulaire->addElement('textarea', 'mails|texte_adhesion_personne_physique', 'Texte adhesion personne physique', array('cols' => 42, 'rows' => 7));
$formulaire->addElement('text'    , 'mails|force_destinataire'              , 'Force le destinaire du mail pour test'                  , array('size' => 30));
$formulaire->addElement('text'    , 'mails|bcc'                             , 'Ajout un email en bcc à tout les emailss'                  , array('size' => 30));


$formulaire->addElement('header'  , ''                                      , 'Config SMTP');
$formulaire->addElement('text'    , 'mails|serveur_smtp'                    , 'Serveur SMTP'                , array('size' => 30));
$formulaire->addElement('advcheckbox', 'mails|tls'                          , 'Use TLS'                     , null, null, array(0, 1));
$formulaire->addElement('text'    , 'mails|port'                            , 'Port'                        , array('size' => 30));
$formulaire->addElement('text'    , 'mails|username'                        , 'Username'                    , array('size' => 30));
$formulaire->addElement('text'    , 'mails|password'                        , 'Password'                    , array('size' => 30));

$formulaire->addElement('header'  , ''                   , 'AFUP');
$formulaire->addElement('text'    , 'afup|raison_sociale', 'Raison Sociale', array('size' => 30));
$formulaire->addElement('textarea', 'afup|adresse'       , 'Adresse'       , array('cols' => 42, 'rows' => 7));
$formulaire->addElement('text'	  , 'afup|code_postal'   , 'Code postal'   , array('size' =>  5));
$formulaire->addElement('text'	  , 'afup|ville'         , 'Ville'         , array('size' => 20));
$formulaire->addElement('text'	  , 'afup|email'         , 'Email'         , array('size' => 30));

$formulaire->addElement('header'  , ''                , 'Paybox');
$formulaire->addElement('text'    , 'paybox_prod|site', 'Prod. site', array('size' => 30));
$formulaire->addElement('text'    , 'paybox_prod|rang', 'Prod. rang', array('size' => 5));
$formulaire->addElement('text'    , 'paybox_test|site', 'Test site' , array('size' => 30));
$formulaire->addElement('text'    , 'paybox_test|rang', 'Test rang' , array('size' => 5));
$formulaire->addElement('radio'   , 'paybox', 'Production', null, 'prod');
$formulaire->addElement('radio'   , 'paybox', 'Test'      , null, 'test');

$formulaire->addElement('header'  , ''                       , 'Planète PHP FR');
$formulaire->addElement('textarea', 'planete|pertinence'     , 'Critère de pertinence', array('cols' => 42, 'rows' => 7));

$formulaire->addElement('header'     , ''                       , 'Divers');
$formulaire->addElement('select'     , 'divers|niveau_erreur'   , 'Niveau erreur'   , array(E_ALL     => 'Toutes',
                                                                                            E_NOTICE  => 'Informations',
                                                                                            E_WARNING => 'Avertissements',
                                                                                            0         => 'Aucune'));
$formulaire->addElement('advcheckbox', 'divers|afficher_erreurs', 'Afficher erreurs', null, null, array(0, 1));

$formulaire->addElement('header', 'boutons'  , '');
$formulaire->addElement('submit', 'soumettre', 'Enregistrer');

$formulaire->addRule('bdd|hote'       , 'Hote manquant'       , 'required');
$formulaire->addRule('bdd|base'       , 'Base manquante'      , 'required');
$formulaire->addRule('bdd|utilisateur', 'Utilisateur manquant', 'required');

if ($formulaire->validate()) {
	$valeurs = $formulaire->exportValues();
	$cle     = 'paybox_' . $valeurs['paybox'];
	unset($valeurs['paybox']);
	$valeurs['paybox|site'] = $valeurs[$cle . '|site'];
	$valeurs['paybox|rang'] = $valeurs[$cle . '|rang'];

    $conf->importer($valeurs);
    if ($conf->enregistrer()) {
        Logs::log('Modification de la configuration');
        afficherMessage('La configuration a été enregistrée', 'index.php?page=configuration');
    } else {
        $smarty->assign('erreur', "Une erreur est survenue lors de l'enregistrement de la configuration");
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));

?>
