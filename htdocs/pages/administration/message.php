<?php

declare(strict_types=1);

use AppBundle\Controller\LegacyController;

// Impossible to access the file itself
/** @var LegacyController $this */
if (!defined('PAGE_LOADED_USING_INDEX')) {
    trigger_error("Direct access forbidden.", E_USER_ERROR);
    exit;
}

    // On supprime ce qui a déjà été écrit dans le buffer de sortie
    // car on va afficher une page "indépendente"
    ob_clean();

    // On affiche la page du message
    $smarty->assign('message', stripslashes((string) $_GET['message']));
    $smarty->assign('url'    , $_GET['url']);
    $smarty->assign('erreur' , $_GET['erreur']);
    $smarty->display('message.html');

    // On s'arrête là pour ne pas afficher le pied de page
    exit;
