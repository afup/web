<?php

// Handler resetstyle.php version 0.2 du 16/03/2004
// pour WikiNi 0.4.1rc (=> à la version du 200403xx) et supérieurs
// Par Charles Népote (c) 2004
// Licence GPL


// Fonctionnement
//
// Cet handler permet à l'utilisateur de revenir à la feuille de style par défaut du site.
// Techniquement :


// Usage :
// http://example.org/PageTest/resetstyle


// A compléter (peut-être un jour) :
//
// -- détecter le fichier par défaut via une variable de configuration
//

$this->SetPersistentCookie('sitestyle','wakka',1);
header("Location: ".$this->href());

?>