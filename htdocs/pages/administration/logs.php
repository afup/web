<?php

if (empty($_GET['numero_page'])) {
    $_GET['numero_page'] = 1;    
}
$smarty->assign('logs'        , AFUP_Logs::obtenirTous($_GET['numero_page']));
$smarty->assign('nombre_pages', AFUP_Logs::obtenirNombrePages());
$smarty->assign('numero_page' , $_GET['numero_page']);
$smarty->assign('url'         , 'index.php?page=logs');

?>