<?php
require_once __DIR__ . '/../sources/Afup/Bootstrap/Cli.php';

echo "Synchronisation Redmine " . date('Y-m-d H:i:s') . PHP_EOL;

$redmine = new AFUP_Base_De_Donnees(
    $conf->obtenir('redmine|hote'),
    $conf->obtenir('redmine|base'),
    $conf->obtenir('redmine|utilisateur'),
    $conf->obtenir('redmine|mot_de_passe')
);

echo " - recuperation membres Afup...\n";
$membresAfup = $bdd->obtenirTous('SELECT id as multipass_remote_uid, login, prenom as firstname, nom as lastname, email as mail, etat as status FROM afup_personnes_physiques WHERE etat = 1');

echo " - recuperation comptes Redmine...\n";
$tmpMembresRedmine = $redmine->obtenirTous('SELECT multipass_remote_uid, login, firstname, lastname, mail, status FROM users WHERE multipass_remote_uid is not null and multipass_remote_uid > 0');
foreach ($tmpMembresRedmine as $mR) {
    $membresRedmine[$mR['multipass_remote_uid']] = $mR;
}

foreach ($membresAfup as $mA) {
    $id = $mA['multipass_remote_uid'];
    if (array_key_exists($id, $membresRedmine)) {
        $mR = $membresRedmine[$id];
        // On teste les différences
        $diff = array_diff($mA, $mR);
        if (count($diff)) {
            $data = array();
            foreach ($diff as $k => $v) {
                $data[] = "$k = '" . mysql_real_escape_string($v) . "'";
            }
            // On modifie l'utilisateur
            echo " - modification: $id\n";
            $redmine->executer("UPDATE users SET " . implode(', ', $data) . " WHERE multipass_remote_uid = '" . $id . "'");
        }
    } else {
        // On crée l'utilisateur
        echo " - creation: $id\n";
        $redmine->executer("INSERT INTO users VALUES(null,
                                                     '" . mysql_real_escape_string($mA['login']) . "',
                                                     sha1(concat('" . mysql_real_escape_string($mA['mail']) . "', now())),
                                                     '" . mysql_real_escape_string($mA['firstname']) . "',
                                                     '" . mysql_real_escape_string($mA['lastname']) . "',
                                                     '" . mysql_real_escape_string($mA['mail']) . "',
                                                     0, 0, 1, null,
                                                     'fr', null, now(), now(),
                                                     'User', null, $id)");
    }
    // Utilisateur traité
    unset($membresRedmine[$id]);
}

// Ceux qui restent sont désactivés
foreach ($membresRedmine as $k => $mR) {
    echo " - desactivation: $k\n";
    $redmine->executer('UPDATE users SET status = 0 WHERE multipass_remote_uid=' . $mR['multipass_remote_uid']);
}

echo "Memoire maxi: " . sprintf("%0.1f", memory_get_peak_usage() / 1024 / 1024) . "Mo\n";
echo "Memoire fin: " . sprintf("%0.1f", memory_get_usage() / 1024 / 1024) . "Mo\n";
echo "Duree: " . sprintf("%0.3f", microtime(true) - $startMicrotime) . "s\n";