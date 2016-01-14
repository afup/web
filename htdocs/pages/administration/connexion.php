<?php

// Impossible to access the file itself
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

// On supprime ce qui a déjà été écrit dans le buffer de sortie
// car on va afficher une page "indépendente"
ob_clean();

// On affiche la page de connexion
$echec = !empty($_GET['echec']) ? (bool) $_GET['echec'] : false;
$page_demandee = isset($_GET['page_demandee']) ? $_GET['page_demandee'] : null;
$smarty->assign('echec', $echec);
$smarty->assign('page_demandee', $page_demandee);
$smarty->display('connexion.html');

// On s'arrête là pour ne pas afficher le pied de page
exit;
?>