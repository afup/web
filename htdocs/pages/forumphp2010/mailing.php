<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';
require_once 'Afup/AFUP_Mailing.php';
$mailing = new AFUP_Mailing($bdd);
$message = '';
$email = isset($_GET['unsuscribe'])?$_GET['unsuscribe']:false;
$nb = isset($_GET['nb'])?$_GET['nb']:'';
$action = isset($_GET['action'])?$_GET['action']:false;
if ($action =='unsuscribe')
{
  $mailing->BlacklistEmail($email);
  $message = "L'adresse $email a bien été retirée de nos listes.";
}
$smarty->assign('message',$message);
$smarty->assign('email',$email);
$smarty->assign('nb',$nb);
$smarty->display('mailing.html');
?>