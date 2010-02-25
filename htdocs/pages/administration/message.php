<?php
    // On supprime ce qui a déjà été écrit dans le buffer de sortie
    // car on va afficher une page "indépendente"
    ob_clean();
    
    // On affiche la page du message
    $smarty->assign('message', stripslashes($_GET['message']));
    $smarty->assign('url'    , $_GET['url']);
    $smarty->assign('erreur' , $_GET['erreur']);
    $smarty->display('message.html');
    
    // On s'arrête là pour ne pas afficher le pied de page
    exit;
?>