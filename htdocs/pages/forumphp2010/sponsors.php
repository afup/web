<?php
require_once '../../include/prepend.inc.php';
require_once dirname(__FILE__) . '/_config.inc.php';

// TODO: Mettre cela dans la base de données

//$partenaires = array();
//$smarty->assign('partenaires', $partenaires);


$smarty->display('sponsors.html');
?>