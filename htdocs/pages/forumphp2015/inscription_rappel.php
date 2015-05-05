<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once dirname(__FILE__).'/../../../sources/Afup/AFUP_Inscriptions_Forum.php';

$inscriptions = new AFUP_Inscriptions_Forum($bdd);
if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['email'])) {
    $inscriptions->ajouterRappel($_POST['email']);
    $smarty->display('inscriptions_rappel.html');
} else {
    header('Location: inscription.php?rappel=invalide');
}