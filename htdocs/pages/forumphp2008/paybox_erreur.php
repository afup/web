<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

require_once 'Afup/AFUP_Inscriptions_Forum.php';
$inscriptions = new AFUP_Inscriptions_Forum($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_ERREUR);

$smarty->display('paybox_erreur.html');
?>