<?php
require_once '../../include/prepend.inc.php';

require_once 'afup/AFUP_Inscriptions_Forum.php';
$inscriptions = new AFUP_Inscriptions_Forum($bdd);
$inscriptions->modifierEtatInscription($_GET['cmd'], AFUP_FORUM_ETAT_ERREUR);

$smarty->display('paybox_erreur.html');
?>