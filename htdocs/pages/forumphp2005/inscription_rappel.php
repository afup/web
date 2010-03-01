<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

require_once 'Afup/AFUP_Inscriptions_Forum.php';
$inscriptions = new AFUP_Inscriptions_Forum($bdd);

$inscriptions->ajouterRappel($_POST['email']);
$smarty->display('inscriptions_rappel.html');
?>