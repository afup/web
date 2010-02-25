<?php
require_once '../../include/prepend.inc.php';
require_once AFUP_CHEMIN_RACINE . 'classes/afup/AFUP_Aperos.php';
$aperos = new AFUP_Aperos($bdd);

$evenements = $aperos->obtenirListe();
$smarty->assign('evenements', $evenements);

$smarty->display('index.html');
?>