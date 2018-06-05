<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Physiques;
use Afup\Site\Utils\Pays;
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$action = verifierAction(array('modifier'));
$smarty->assign('action', $action);


$personnes_physiques = new Personnes_Physiques($bdd);


$pays = new Pays($bdd);

$formulaire = &instancierFormulaire();
$champs = $personnes_physiques->obtenir($droits->obtenirIdentifiant());
unset($champs['mot_de_passe']);
$formulaire->setDefaults($champs);

$formulaire->addElement('header'  , ''                         , 'Informations');
$formulaire->addElement('text'    , 'email'                    , 'Email'          , array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('textarea', 'adresse'                  , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
$formulaire->addElement('text'    , 'code_postal'              , 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
$formulaire->addElement('text'    , 'ville'                    , 'Ville'          , array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('select'  , 'id_pays'                  , 'Pays'           , $pays->obtenirPays());
$formulaire->addElement('text'    , 'telephone_fixe'           , 'Tél. fixe'      , array('size' => 20, 'maxlength' => 20));
$formulaire->addElement('text'    , 'telephone_portable'       , 'Tél. portable'  , array('size' => 20, 'maxlength' => 20));
$officesCollection = new \AppBundle\Offices\OfficesCollection();
$offices = ['' => '-Aucune-'];
foreach ($officesCollection->getOrderedLabelsByKey() as $key => $label) {
    $offices[$key] = $label;
}
$formulaire->addElement('select'  , 'nearest_office'           , 'Antenne la plus proche', $offices);

$formulaire->addElement('header'  , ''                         , 'Paramètres');
$formulaire->addElement('text'    , 'login'                    , 'Login'          , array('size' => 30, 'maxlength' => 30));
$formulaire->addElement('static',   'note'                 , '    '           , 'Ne renseignez le mot de passe et sa confirmation que si vous souhaitez le changer');
$formulaire->addElement('password', 'mot_de_passe'             , 'Mot de passe'   , array('size' => 30));
$formulaire->addElement('password', 'confirmation_mot_de_passe', ''               , array('size' => 30));

$formulaire->addElement('header'  , 'boutons'                  , '');
$formulaire->addElement('submit'  , 'soumettre'                , ucfirst($action));

$formulaire->addRule('email'       , 'Email manquant'       , 'required');
$formulaire->addRule('email'       , 'Email invalide'       , 'email');
$formulaire->addRule('adresse'     , 'Adresse manquante'    , 'required');
$formulaire->addRule('code_postal' , 'Code postal manquant' , 'required');
$formulaire->addRule('ville'       , 'Ville manquante'      , 'required');
$formulaire->addRule('login'       , 'Login manquant'       , 'required');
$formulaire->addRule(array('mot_de_passe', 'confirmation_mot_de_passe'), 'Le mot de passe et sa confirmation ne concordent pas', 'compare');


if ($formulaire->validate()) {
    $ok = $personnes_physiques->modifierCoordonnees($droits->obtenirIdentifiant(),
        $formulaire->exportValue('login'),
        $formulaire->exportValue('mot_de_passe'),
        $formulaire->exportValue('email'),
        $formulaire->exportValue('adresse'),
        $formulaire->exportValue('code_postal'),
        $formulaire->exportValue('ville'),
        $formulaire->exportValue('id_pays'),
        $formulaire->exportValue('telephone_fixe'),
        $formulaire->exportValue('telephone_portable'),
        $formulaire->exportValue('nearest_office')
    );

    if ($ok) {
        Logs::log('Modification de la personne physique par l\'utilisateur (' . $_GET['id'] . ')');
        afficherMessage('Vos coordonnées ont été mises à jour', 'index.php?page=membre_coordonnees');
    } else {
        $smarty->assign('erreur', 'Une erreur est survenue lors de la modification de vos coordonnées');
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
