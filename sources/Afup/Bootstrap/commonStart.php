<?php

// Inclusion de l'autoload de composer
require_once dirname(__FILE__) . '/../../../vendor/autoload.php';

// Configuration du composant de traduction
$lang = 'fr';
$langs = ['fr', 'en'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $langs)) {
    $lang = $_GET['lang'];
}
$translator = new \Symfony\Component\Translation\Translator($lang);
$translator->addLoader('xliff', new \Symfony\Component\Translation\Loader\XliffFileLoader());
$translator->addResource('xliff', dirname(__FILE__) . '/../../../translations/inscription.en.xlf', 'en');
$translator->addResource('xliff', dirname(__FILE__) . '/../../../translations/cfp.en.xlf', 'en');
$translator->setFallbackLocales(array('fr'));
if (isset($smarty)) {
    $smarty->register_modifier('trans', [$translator, 'trans']);
}


$debug = false;
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'afup.dev') {
    $debug = true;
}

define('AFUP_FORUM_ETAT_CREE', 0);
define('AFUP_FORUM_ETAT_ANNULE', 1);
define('AFUP_FORUM_ETAT_ERREUR', 2);
define('AFUP_FORUM_ETAT_REFUSE', 3);
define('AFUP_FORUM_ETAT_REGLE', 4);
define('AFUP_FORUM_ETAT_INVITE', 5);
define('AFUP_FORUM_ETAT_ATTENTE_REGLEMENT', 6);
define('AFUP_FORUM_ETAT_CONFIRME', 7);
define('AFUP_FORUM_ETAT_A_POSTERIORI', 8);

define('AFUP_FORUM_FACTURE_A_ENVOYER', 0);
define('AFUP_FORUM_FACTURE_ENVOYEE', 1);
define('AFUP_FORUM_FACTURE_RECUE', 2);

define('AFUP_FORUM_PREMIERE_JOURNEE', 0);
define('AFUP_FORUM_DEUXIEME_JOURNEE', 1);
define('AFUP_FORUM_2_JOURNEES', 2);
define('AFUP_FORUM_2_JOURNEES_AFUP', 3);
define('AFUP_FORUM_2_JOURNEES_ETUDIANT', 4);
define('AFUP_FORUM_2_JOURNEES_PREVENTE', 5);
define('AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE', 6);
define('AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE', 7);
define('AFUP_FORUM_2_JOURNEES_COUPON', 8);
define('AFUP_FORUM_ORGANISATION', 9);
define('AFUP_FORUM_SPONSOR', 10);
define('AFUP_FORUM_PRESSE', 11);
define('AFUP_FORUM_CONFERENCIER', 12);
define('AFUP_FORUM_INVITATION', 13);
define('AFUP_FORUM_PROJET', 14);
define('AFUP_FORUM_2_JOURNEES_SPONSOR', 15);
define('AFUP_FORUM_PROF', 16);
define('AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE', 17);
define('AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE', 18);
define('AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION', 19);
define('AFUP_FORUM_PREMIERE_JOURNEE_AFUP', 20);
define('AFUP_FORUM_DEUXIEME_JOURNEE_AFUP', 21);
define('AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT', 22);
define('AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT', 23);
define('AFUP_FORUM_EARLY_BIRD', 100);
define('AFUP_FORUM_EARLY_BIRD_AFUP', 101);
define('AFUP_FORUM_LATE_BIRD', 102);
define('AFUP_FORUM_LATE_BIRD_AFUP', 103);
define('AFUP_FORUM_LATE_BIRD_PREMIERE_JOURNEE', 105);
define('AFUP_FORUM_LATE_BIRD_DEUXIEME_JOURNEE', 106);
define('AFUP_FORUM_CFP_SUBMITTER', 107);
define('AFUP_FORUM_SPECIAL_PRICE', 108);

define('AFUP_FORUM_REGLEMENT_CARTE_BANCAIRE', 0);
define('AFUP_FORUM_REGLEMENT_CHEQUE', 1);
define('AFUP_FORUM_REGLEMENT_VIREMENT', 2);
define('AFUP_FORUM_REGLEMENT_AUCUN', 3);
define('AFUP_FORUM_REGLEMENT_A_POSTERIORI', 4);


define('AFUP_PERSONNES_MORALES', 1);
define('AFUP_COTISATION_PERSONNE_MORALE', 150);
define('AFUP_PERSONNE_MORALE_SEUIL', 3);

define('AFUP_RAISON_SOCIALE', 'AFUP');
define('AFUP_ADRESSE', "32, Boulevard de Strasbourg\nCS 30108");
define('AFUP_CODE_POSTAL', '75468');
define('AFUP_VILLE', 'Paris Cedex 10');
define('AFUP_EMAIL', 'bureau@afup.org');
define('AFUP_SIRET', '500 869 011 00022');
define('AFUP_NUMERO_TVA', 'NUMERO_A_AJOUTER');

$AFUP_Tarifs_Forum = array(
    AFUP_FORUM_INVITATION => 0,
    AFUP_FORUM_ORGANISATION => 0,
    AFUP_FORUM_SPONSOR => 0,
    AFUP_FORUM_PRESSE => 0,
    AFUP_FORUM_CONFERENCIER => 0,
    AFUP_FORUM_PROJET => 0,
    AFUP_FORUM_PROF => 0,
    AFUP_FORUM_PREMIERE_JOURNEE => 150,
    AFUP_FORUM_DEUXIEME_JOURNEE => 150,
    AFUP_FORUM_2_JOURNEES => 250,
    AFUP_FORUM_2_JOURNEES_AFUP => 150,
    AFUP_FORUM_PREMIERE_JOURNEE_AFUP => 100,
    AFUP_FORUM_DEUXIEME_JOURNEE_AFUP => 100,
    AFUP_FORUM_2_JOURNEES_ETUDIANT => 150,
    AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT => 100,
    AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT => 100,
    AFUP_FORUM_2_JOURNEES_PREVENTE => 150,
    AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE => 150,
    AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION => 150,
    AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE => 100,
    AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE => 100,
    AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => 150,
    AFUP_FORUM_2_JOURNEES_COUPON => 200,
    AFUP_FORUM_2_JOURNEES_SPONSOR => 200,
    AFUP_FORUM_SPECIAL_PRICE => 0,

);

$GLOBALS['AFUP_Tarifs_Forum'] = $AFUP_Tarifs_Forum;

$AFUP_Tarifs_Forum_Lib = array(
    AFUP_FORUM_INVITATION => 'Invitation',
    AFUP_FORUM_ORGANISATION => 'Organisation',
    AFUP_FORUM_PROJET => 'Projet PHP',
    AFUP_FORUM_SPONSOR => 'Sponsor',
    AFUP_FORUM_PRESSE => 'Presse',
    AFUP_FORUM_PROF => 'Enseignement supérieur',
    AFUP_FORUM_CONFERENCIER => 'Conferencier',
    AFUP_FORUM_PREMIERE_JOURNEE => 'Jour 1 ',
    AFUP_FORUM_DEUXIEME_JOURNEE => 'Jour 2',
    AFUP_FORUM_2_JOURNEES => '2 Jours',
    AFUP_FORUM_2_JOURNEES_AFUP => '2 Jours AFUP',
    AFUP_FORUM_PREMIERE_JOURNEE_AFUP => 'Jour 1 AFUP',
    AFUP_FORUM_DEUXIEME_JOURNEE_AFUP => 'Jour 2 AFUP',
    AFUP_FORUM_2_JOURNEES_ETUDIANT => '2 Jours Etudiant',
    AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT => 'Jour 1 Etudiant',
    AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT => 'Jour 2 Etudiant',
    AFUP_FORUM_2_JOURNEES_PREVENTE => '2 Jours prévente',
    AFUP_FORUM_2_JOURNEES_AFUP_PREVENTE => '2 Jours AFUP prévente',
    AFUP_FORUM_2_JOURNEES_PREVENTE_ADHESION => '2 Jours prévente + adhésion',
    AFUP_FORUM_2_JOURNEES_ETUDIANT_PREVENTE => '2 Jours Etudiant prévente',
    AFUP_FORUM_2_JOURNEES_COUPON => '2 Jours avec coupon de réduction',
    AFUP_FORUM_2_JOURNEES_SPONSOR => '2 Jours par Sponsor',
    AFUP_FORUM_PREMIERE_JOURNEE_ETUDIANT_PREVENTE => '',
    AFUP_FORUM_DEUXIEME_JOURNEE_ETUDIANT_PREVENTE => '',
    AFUP_FORUM_SPECIAL_PRICE => 'Tarif Spécial',
);

$GLOBALS['AFUP_Tarifs_Forum_Lib'] = $AFUP_Tarifs_Forum_Lib;


// Initialisation de ting
$services = new \CCMBenchmark\Ting\Services();
$services->get('ConnectionPool')->setConfig([
    'main' => [
        'namespace' => '\CCMBenchmark\Ting\Driver\Mysqli',
        'master' => [
            'host'      => $GLOBALS['AFUP_CONF']->obtenir('database_host'),
            'user'      => $GLOBALS['AFUP_CONF']->obtenir('database_user'),
            'password'  => $GLOBALS['AFUP_CONF']->obtenir('database_password'),
            'port'      => 3306,
        ]
    ]
]);

$services
    ->get('MetadataRepository')
    ->batchLoadMetadata(
        'AppBundle\Event\Model\Repository',
        __DIR__ . '/../Event/Model/Repository/*.php',
        ['default' => ['database' => $GLOBALS['AFUP_CONF']->obtenir('database_name')]]
    )
;
$services->set('security.csrf.token_manager', function(){
    return new Symfony\Component\Security\Csrf\CsrfTokenManager();
});
