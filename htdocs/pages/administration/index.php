<?php
require_once '../../include/prepend.inc.php';
// Gestion des droits
require_once 'afup/AFUP_Utils.php';
$droits = AFUP_Utils::fabriqueDroits($bdd);

if (!isset($_GET['page'])) {
    $_GET['page'] = 'connexion';
}
if (!empty($_POST['connexion'])) {
    $droits->seConnecter($_POST['utilisateur'], $_POST['mot_de_passe']);
}
if (!empty($_POST['motdepasse_perdu'])) {
    require_once 'afup/AFUP_Personnes_Physiques.php';
    $personnes_physiques = new AFUP_Personnes_Physiques($bdd);
    $result = $personnes_physiques->envoyerMotDePasse($_POST['utilisateur'], $_POST['email']);

    if (!$result) {
        $_GET['echec'] = 1;
        $_GET['page'] = 'mot_de_passe_perdu';
    } else {
        afficherMessage('Votre mot de passe vous a été envoyé par mail', 'index.php');
    }
}

if (!empty($_POST['inscription'])) {
    // Initialisation de AFUP_Log
    require_once 'afup/AFUP_Logs.php';
    AFUP_Logs::initialiser($bdd, $droits->obtenirIdentifiant());
	require_once 'inscription.php';
}

if ($_GET['page'] == 'se_deconnecter') {
	$droits->seDeconnecter();
	header('Location: index.php?page=connexion');
	exit;
}

if (!empty($_GET['hash'])) {
    $droits->seDeconnecter();
    $droits->seConnecterEnAutomatique($_GET['hash']);
}

if (!$droits->estConnecte() and $_GET['page'] != 'connexion' and $_GET['page'] != 'mot_de_passe_perdu' and
$_GET['page'] != 'message' and $_GET['page'] != 'inscription') {
    header('Location: index.php?page=connexion&echec=' . $droits->verifierEchecConnexion());
    exit;
}
// On vérifie que l'utilisateur a le droit d'accéder à la page
require_once '../../include/pages.inc.php';
$droits->chargerToutesLesPages($pages);
if (!$droits->verifierDroitSurLaPage($_GET['page'])) {
    afficherMessage("Vous n'avez pas le droit d'accéder à cette page", 'index.php');
}
// Initialisation de AFUP_Log
require_once 'afup/AFUP_Logs.php';
AFUP_Logs::initialiser($bdd, $droits->obtenirIdentifiant());
// On inclut le fichier PHP de la page
$smarty->assign('ctx_login', $_GET['ctx_login']);
$smarty->assign('id_page', $_GET['page']);
$smarty->assign('titre_page', obtenirTitre($pages, $_GET['page']));
$smarty->assign('web_path', $conf->obtenir('web|path'));
$smarty->assign('pages', $droits->dechargerToutesLesPages());

if ($_GET['page']=='index' or !file_exists(AFUP_CHEMIN_RACINE . 'pages/administration/' . $_GET['page'] . '.php')) {
	$_GET['page'] = 'accueil';
}

require_once AFUP_CHEMIN_RACINE . 'pages/administration/' . $_GET['page'] . '.php';
// Affichage de la page
$smarty->display('entete.html');
$smarty->display($_GET['page'] . '.html');
$smarty->display('pied_de_page.html');