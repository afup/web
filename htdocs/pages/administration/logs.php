<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

if (empty($_GET['numero_page'])) {
    $_GET['numero_page'] = 1;    
}
$smarty->assign('logs'        , AFUP_Logs::obtenirTous($_GET['numero_page']));
$smarty->assign('nombre_pages', AFUP_Logs::obtenirNombrePages());
$smarty->assign('numero_page' , $_GET['numero_page']);
$smarty->assign('url'         , 'index.php?page=logs');

?>