<?php

// Impossible to access the file itself
use Afup\Site\Association\Personnes_Morales;
use Afup\Site\Utils\Logs;
use Afup\Site\Utils\Pays;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Assert\Assertion;

/** @var \Afup\Site\Droits $droits */
/** @var \AppBundle\Controller\LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

$userRepository = $this->get(UserRepository::class);
$personnes_morales = new Personnes_Morales($bdd);

$identifiant = $droits->obtenirIdentifiant();
$user = $userRepository->get($identifiant);
Assertion::notNull($user);
if (!$user->getCompanyId()) {
    // Cette page est reservee aux membres appartenants à une personne morale
    header('HTTP/1.1 403 FORBIDDEN');
    exit;
}

$action='modifier';
$smarty->assign('action', $action);
$users = $userRepository->search('lastname', 'asc', null, $user->getCompanyId());

$pays = new Pays($bdd);

$formulaire = instancierFormulaire();
$champs = $personnes_morales->obtenir($user->getCompanyId());
unset($champs['mot_de_passe']);
$formulaire->setDefaults($champs);

$formulaire->addElement('header'  , ''                   , 'Informations');
$formulaire->addElement('text'    , 'raison_sociale'     , 'Raison sociale' , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'siret'              , 'Siret'          , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('textarea', 'adresse'            , 'Adresse'        , array('cols' => 42, 'rows'      => 10));
$formulaire->addElement('text'    , 'code_postal'        , 'Code postal'    , array('size' =>  6, 'maxlength' => 10));
$formulaire->addElement('text'    , 'ville'              , 'Ville'          , array('size' => 30, 'maxlength' => 50));
$formulaire->addElement('select'  , 'id_pays'            , 'Pays'           , $pays->obtenirPays());

$formulaire->addElement('header'  , ''                   , 'Contact administratif');
$formulaire->addElement('select'  , 'civilite'           , 'Civilité'       , array('M.', 'Mme', 'Mlle'));
$formulaire->addElement('text'    , 'nom'                , 'Nom'            , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'prenom'             , 'Prénom'         , array('size' => 30, 'maxlength' => 40));
$formulaire->addElement('text'    , 'email'              , 'Email'          , array('size' => 30, 'maxlength' => 100));
$formulaire->addElement('text'    , 'telephone_fixe'     , 'Tél. fixe'      , array('size' => 20, 'maxlength' => 20));
$formulaire->addElement('text'    , 'telephone_portable' , 'Tél. portable'  , array('size' => 20, 'maxlength' => 20));

$formulaire->addElement('header'  , ''                   , 'Membres associés');
foreach ($users as $user) {
    $nom = $user->getLastName() . ' ' . $user->getFirstName()[0];
    if (User::STATUS_INACTIVE === $user->getStatus()) {
        $nom = "<del>$nom</del>";
    }
    $formulaire->addElement('static', 'info', $nom . '.');
}

$formulaire->addElement('header'  , ''                   , 'Paramètres');
$formulaire->addElement('select'  , 'etat'               , 'Etat'        , array(AFUP_DROITS_ETAT_ACTIF   => 'Actif',
                                                                               AFUP_DROITS_ETAT_INACTIF => 'Inactif'));

$formulaire->addElement('header'  , 'boutons'            , '');
$formulaire->addElement('submit'  , 'soumettre'          , ucfirst($action));

$formulaire->addRule('nom'         , 'Nom manquant'         , 'required');
$formulaire->addRule('prenom'      , 'Prénom manquant'      , 'required');
$formulaire->addRule('email'       , 'Email manquant'       , 'required');
$formulaire->addRule('email'       , 'Email invalide'       , 'email');
$formulaire->addRule('raison_sociale', 'Raison sociale manquante', 'required');
$formulaire->addRule('adresse'       , 'Adresse manquante'       , 'required');
$formulaire->addRule('code_postal'   , 'Code postal manquant'    , 'required');
$formulaire->addRule('ville'         , 'Ville manquante'         , 'required');

if ($formulaire->validate()) {
    $ok = $personnes_morales->modifier(
        $user->getCompanyId(),
        $formulaire->exportValue('civilite'),
        $formulaire->exportValue('nom'),
        $formulaire->exportValue('prenom'),
        $formulaire->exportValue('email'),
        $formulaire->exportValue('raison_sociale'),
        $formulaire->exportValue('siret'),
        $formulaire->exportValue('adresse'),
        $formulaire->exportValue('code_postal'),
        $formulaire->exportValue('ville'),
        $formulaire->exportValue('id_pays'),
        $formulaire->exportValue('telephone_fixe'),
        $formulaire->exportValue('telephone_portable'),
        $formulaire->exportValue('etat')
    );

    if ($ok) {
        Logs::log('Modification de la personne morale ' . $formulaire->exportValue('raison_sociale') . ' (' . $_GET['id'] . ')');
        afficherMessage('La personne morale a été ' . (($action == 'ajouter') ? 'ajoutée' : 'modifiée'), 'index.php?page=membre_personne_morale');
    } else {
        $smarty->assign('erreur', 'Une erreur est survenue lors de ' . (($action == 'ajouter') ? "l'ajout" : 'la modification') . ' de la personne morale');
    }
}

$smarty->assign('formulaire', genererFormulaire($formulaire));
