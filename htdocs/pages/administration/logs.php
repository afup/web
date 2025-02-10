<?php

declare(strict_types=1);

// Impossible to access the file itself
use Afup\Site\Utils\Logs;

if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

if (empty($_GET['numero_page'])) {
    $_GET['numero_page'] = 1;
}
$smarty->assign('logs'        , Logs::obtenirTous($_GET['numero_page']));
$smarty->assign('nombre_pages', Logs::obtenirNombrePages());
$smarty->assign('numero_page' , $_GET['numero_page']);
$smarty->assign('url'         , 'index.php?page=logs');
