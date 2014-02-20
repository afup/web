<?php
require_once __DIR__ . '/../sources/Afup/Bootstrap/Cli.php';

echo "Synchronisation Sympa " . date('Y-m-d H:i:s') . PHP_EOL;

$sympaBdd = new AFUP_Base_De_Donnees(
    $conf->obtenir('sympa|hote'),
    $conf->obtenir('sympa|base'),
    $conf->obtenir('sympa|utilisateur'),
    $conf->obtenir('sympa|mot_de_passe')
);

echo " - recuperation membres à jour de cotisation...\n";
require_once dirname(__FILE__).'/../sources/Afup/AFUP_Assemblee_Generale.php';
$assemblee = new AFUP_Assemblee_Generale($bdd);
$membresAfup = explode(';', strtolower($assemblee->obtenirListeEmailPersonnesAJourDeCotisation()));
require_once dirname(__FILE__).'/../sources/Afup/AFUP_Personnes_Physiques.php';
$personnes = new AFUP_Personnes_Physiques($bdd);
$infoMembres = $personnes->obtenirListe('lower(email) as email,nom,prenom', 'email', false, false, true, null);

echo " - recuperation de toutes les listes Sympa et de leurs lecteurs...\n";
require_once dirname(__FILE__).'/../sources/Afup/AFUP_Sympa.php';
$sympa = new AFUP_Sympa($sympaBdd, $conf->obtenir('sympa|directory'));
$tmpListes = $sympa->getAllMailingList();
$usersSympa = $sympa->getAllUsers();

foreach ($tmpListes as $k => $tmpL) {
    if (substr($tmpL['sujet'], 0, 12) != 'Antenne AFUP') { // On ne traite pas les ML des antennes locales car des non membres peuvent y être inscrits
        $listes[$k] = $sympa->getInscritsMailingList($k);
    }
}

echo " - analyse différences...\n";
foreach ($membresAfup as $ma) {
    // L'utilisateur est-il connu de sympa ?
    $userSympa = isset($usersSympa[$ma]) ? $usersSympa[$ma] : null;
    $m = $infoMembres[$ma];
    $newUser = false;
    if (!$userSympa) {
        echo " - creation user " . $ma . PHP_EOL;
        $sympa->createUser($ma, $m['prenom'] . ' ' . $m['nom']);
        $newUser = true;
    }
    foreach ($listes as $kl => $l) {
        $idx = array_search($ma, $l);
        if ($idx !== false) {
            unset($listes[$kl][$idx]); // marqué comme traité
        } else {
            if ($newUser && in_array($kl, array('emploi', 'entraide'))) {
                // On l'ajoute si nouveau mais on ne force pas car il a pu se déconnecter de ces listes
                echo " > ajout email à " . $kl . " : " . $ma . PHP_EOL;
                $sympa->subscribe($ma, $kl, $m['prenom'] . ' ' . $m['nom']);
            }
            if (in_array($kl, array('membres'))) {
                // On l'ajoute toujours
                echo " > ajout email à " . $kl . " : " . $ma . PHP_EOL;
                $sympa->subscribe($ma, $kl, $m['prenom'] . ' ' . $m['nom']);
            }
        }
    }
}

echo " - effacement anciens membres...\n";
foreach ($listes as $kl2 => $l2) {
    // Il reste des noms donc ils ne sont plus membres AFUP => on les efface de toutes les listes
    foreach ($l2 as $supp) {
        if (in_array($kl2, array('antenne', 'bureau-interne', 'livre-blanc', 'organisation', 'cs'))) {
            echo " # ALERTE $kl2 : l'email $supp ne fait pas parti des membres AFUP\n";
        } else {
            echo " - effacement user $supp de $kl2\n";
            $sympa->unsubscribe($supp, $kl2);
            $sympa->deleteUser($supp);
        }
    }
}

echo "Memoire maxi: " . sprintf("%0.1f", memory_get_peak_usage() / 1024 / 1024) . "Mo\n";
echo "Memoire fin: " . sprintf("%0.1f", memory_get_usage() / 1024 / 1024) . "Mo\n";
echo "Duree: " . sprintf("%0.3f", microtime(true) - $startMicrotime) . "s\n";
