<?php

class paybox {

// Variables privées
var $site;
var $rang;
var $total;
var $cmd;
var $porteur;

var $effectue;
var $refuse;
var $annule;
var $erreur;

var $opt;
var $langue;
var $devise;
var $identifiant;

var $output;
var $txt;
var $wait;
var $boutpi;
var $bkgd;

var $date;

// constructeur et initialiseur
function paybox() {
    $this->site = 'Aucun site';
    $this->rang = 'Aucun rang';
    $this->total = 'Aucun total';
    $this->cmd = 'Aucune commande';
    $this->porteur = 'Aucun porteur';
    $this->effectue = '';
    $this->refuse = '';
    $this->annule = '';
    $this->erreur = '';
    $this->opt = '';
    $this->langue = 'FRA';
    $this->devise = 978;
    $this->identifiant = '';
    $this->annule = '';
    $this->erreur = '';
    $this->date = '';
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_SITE
////////////////////////////////////////////////////////////////
function set_site($site) {
    // Variable exactement égale à 7 chiffres
    $erreur = Paybox::check_site($site);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_SITE', $erreur);
        return;
    }
    $this->site = $site;
    return true;
}

function check_site($site) {
    if (7 != strlen($site)) {
        return "IBS_SITE: Longueur de la variable incorrecte: il faut 7 "
               . "chiffres.";
    } 
    if (preg_match('/[^0-9]/', $site)) {
        return "IBS_SITE : Caractère(s) incorrect(s). Le site ne doit "
               . "contenir que des chiffres.";
    }
    if (!preg_match('/^\d{7}$/', $site)) {
        return "IBS_SITE: Caractère(s) incorrect(s). Le site ne doit "
               . "contenir que des chiffres.";
    }
    return true;
}

function get_site() {
    return($this->site);
}


////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_RANG
////////////////////////////////////////////////////////////////
function set_rang($rang) {
    // Variable exactement égale à 2 ou 3 chiffres
    $erreur = Paybox::check_rang($rang);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_RANG', $erreur);
        return;
    }
    $this->rang = $rang;
}

function check_rang($rang) {
    if (2 > strlen($rang) || 4 < strlen($rang)) {
        return "IBS_RANG: Longueur de la variable incorrecte. (2 chiffres "
               . "pour les tests | 3 chiffres en prod).";
    }
    if (!preg_match('/^\d{2,3}$/', $rang)) {
        return "IBS_RANG: Caractère(s) incorrect(s). Le rang ne doit "
               . "contenir que des chiffres.";
    }
    return true;
}

function get_rang() {
    return($this->rang);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_TOTAL
////////////////////////////////////////////////////////////////
function set_total($total) {
    $erreur = Paybox::check_total($total);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_TOTAL', $erreur);
        return;
    }
    $this->total = $total;
}

function check_total($total) {
    // Variable comprise entre 3 et 10 chiffres
    if (100 > $total) {
        return "IBS_TOTAL: Le montant doit être au moins égal à 1 euro.";
    }
    if ((1 > strlen($total)) || (10 < strlen($total))) {
        return "IBS_TOTAL: Longeur de la variable incorrecte: il faut au "
               . "moins 1 chiffre et au plus 10 chiffres.";
    }
    if (preg_match('/[^0-9]/', $total)) {
        return "IBS_TOTAL: Caractère(s) incorrect(s). Le montant ne doit "
               . "contenir que des chiffres, pas même de points. Il est "
               . "exprimé en centimes d'euros.";
    }
    return true;
}

function get_total() {
    return($this->total);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_CMD
////////////////////////////////////////////////////////////////
function set_cmd($cmd) {
    $erreur = Paybox::check_cmd($cmd);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_CMD', $erreur);
        return;
    }
     $this->cmd = $cmd;
}

function check_cmd($cmd) {
    if (1 > strlen($cmd)) {
        return "IBS_CMD: La longueur de la commande est trop petite.";
    }
    if (250 < strlen($cmd)) {
        return "IBS_CMD: La longueur de la commande est trop grande "
	       . "(255 caractères maximum).";
    }
    if (preg_match('/[^a-z0-9\- :;_\.]/is', $cmd)) {
        return "IBS_CMD: la commande peut contenir des lettres, chiffres, "
	       . "soulignés, tirets, points, deux-points et points-virgules.";
    }
    return true;
}

function get_cmd() {
    return($this->cmd);
}


////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_PORTEUR
////////////////////////////////////////////////////////////////
function set_porteur($porteur) {
    $erreur = Paybox::check_porteur($porteur);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_PORTEUR', $erreur);
        return;
    }
    $this->porteur = $porteur;
}

function check_porteur($porteur) {
    if (5 > strlen($porteur)) {
        return "L'adresse du porteur est trop courte. Elle doit comporter au "
               . "moins 5 caractères.";
    }
    if (80 < strlen($porteur)) {
        return "L'adresse du porteur est trop longue. Elle doit comporter au "
               . "plus 80 caractères.";
    }
    if (preg_match('/[^a-z0-9\- :;_\.@]/is', $porteur)) {
        return "IBS_PORTEUR: la commande peut contenir des lettres, chiffres, "
               . "soulignés, tirets, deux-points, points-virgules ou "
               . "arobases (@).";
    }
    return true;
}

function get_porteur() {
    return($this->porteur);
}

/*
////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_RETOUR
////////////////////////////////////////////////////////////////
function set_retour($retour) {
    // Variable de 3 a 150 caracteres
    if ((strlen($retour) < 3) || (strlen($retour) > 150)) {
        $this->paybox_erreur("IBS_RETOUR","Longueur de la variable incorrecte. (au moins 3 caracteres et au plus 150 caracteres).");
    }
    $this->retour = $retour;
}

function get_retour() {
    return($this->retour);
}
*/

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_EFFECTUE
////////////////////////////////////////////////////////////////
function set_effectue($effectue) {
    $erreur = Paybox::check_effectue($effectue);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_EFFECTUE', $erreur);
        return;
    }
    $this->effectue = $effectue;
}

function check_effectue($effectue) {
    if (12 > strlen($effectue)) {
        return "L'adresse de la page de retour après succès est trop courte. "
               . "Elle doit comporter au moins 12 caractères.";
    }
    if (150 < strlen($effectue)) {
        return "L'adresse de la page de retour après succès est trop longue. "
               . "Elle doit comporter au plus 150 caractères.";
    }
    if (preg_match('/[\"\'$]/is', $effectue)) {
        return "IBS_EFFECTUE: L'adresse ne doit pas contenir de guillemets "
               . "ou de signe dollar.";
    }
    return true;
}

function get_effectue() {
    return($this->effectue);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_REFUSE
////////////////////////////////////////////////////////////////
function set_refuse($refuse) {
    $erreur = Paybox::check_refuse($refuse);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_REFUSE', $erreur);
        return;
    }
    $this->refuse = $refuse;
}

function check_refuse($refuse) {
    if (12 > strlen($refuse)) {
        return "L'adresse de la page de retour après paiement refusé est "
               . "trop courte. Elle doit comporter au moins 12 caractères.";
    }
    if (150 < strlen($refuse)) {
        return "L'adresse de la page de retour apres paiement refusé est "
               . "trop longue. Elle doit comporter au plus 150 caractères.";
    }
    if (preg_match('/[\"\'$]/is', $refuse)) {
        return "IBS_REFUSE: L'adresse ne doit pas contenir de guillemets ou "
               . "de signe dollar.";
    }
    return true;
}

function get_refuse() {
    return($this->refuse);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_ANNULE
////////////////////////////////////////////////////////////////
function set_annule($annule) {
    $erreur = Paybox::check_annule($annule);
    if (is_string($erreur)) {
        $this->paybox_erreur('IBS_ANNULE', $erreur);
        return;
    }
    $this->annule = $annule;
}

function check_annule($annule) {
    if (12 > strlen($annule)) {
        return "L'adresse de la page de retour après paiement annulé est "
               . "trop courte. Elle doit comporter au moins 12 caractères.";
    }
    if (150 < strlen($annule)) {
        return "L'adresse de la page de retour apres paiement annulé est "
               . "trop longue. Elle doit comporter au plus 150 caractères.";
    }
    if (preg_match('/[\"\'$]/is', $annule)) {
        return "IBS_ANNULE: L'adresse ne doit pas contenir de guillemets ou "
               . "de signe dollar.";
    }
    return true;
}

function get_annule() {
    return($this->annule);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_ERREUR
////////////////////////////////////////////////////////////////
function set_erreur($erreur) {
    $lerreur = Paybox::check_erreur($erreur);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_ANNULE', $lerreur);
        return;
    }
    $this->erreur = $erreur;
}

function check_erreur($erreur) {
    if (12 > strlen($erreur)) {
        return "L'adresse de la page de retour après erreur est trop "
               . "courte. Elle doit comporter au moins 12 caractères.";
    }
    if (150 < strlen($erreur)) {
        return "L'adresse de la page de retour après erreur est trop "
               . "longue. Elle doit comporter au plus 150 caractères.";
    }
    if (preg_match('/[\"\'$]/is', $erreur)) {
        return "IBS_ERREUR: L'adresse ne doit pas contenir de guillemets ou "
               . "de signe dollar.";
    }
    return true;
}

function get_erreur() {
    return($this->erreur);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_OUTPUT
////////////////////////////////////////////////////////////////
function set_output($output) {
    $lerreur = Paybox::check_output($output);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_OUTPUT', $lerreur);
        return;
    }
    $this->output = $output;
}

function check_output($output) {
    if (!in_array($output, array('A','B','C','D','E'))) {
        return "IBS_OUTPUT: le type de retour doit être A, B, C, D ou E. "
               . "Consultez la documentation Paybox pour plus de détails.";
    }
    return true;
}

function get_output() {
    return($this->output);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_LANGUE
////////////////////////////////////////////////////////////////
function set_langue($langue) {
    $lerreur = Paybox::check_langue($langue);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_LANGUE', $lerreur);
        return;
    }
    $this->langue = $langue;
}

function check_langue($langue) {
    if (!in_array($langue, array("FRA","GBR","ESP","ITA","DEU"))) {
        return "IBS_LANGUE: le type de langue doit être 'FRA', 'GBR', "
               . "'ESP', 'ITA' ou 'DEU'. Consultez la documentation Paybox "
               . "pour plus de détails.";
    }
    return true;
}

function get_langue() {
    return($this->langue);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_DEVISE
////////////////////////////////////////////////////////////////
function set_devise($devise) {
    $lerreur = Paybox::check_devise($devise);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_DEVISE', $lerreur);
        return;
    }
    $this->devise = $devise;
}

function check_devise($devise) {
    if (!is_int($devise)) {
        return "IBS_DEVISE ($devise): La devise doit être le code numérique de la "
               . "monnaie considérée (norme ISO-4217). Exemple: 978 pour "
               . "l'euro.";
    }
    return true;
}

function get_devise() {
    return($this->devise);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_IDENTIFIANT
////////////////////////////////////////////////////////////////
function set_identifiant($identifiant) {
    $lerreur = Paybox::check_identifiant($identifiant);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_IDENTIFIANT', $lerreur);
        return;
    }
    $this->identifiant = $identifiant;
}

function check_identifiant($identifiant) {
    if (!preg_match('/[0-9]{1,9}$/', $identifiant)) {
        return "IBS_IDENTIFIANT: L'identifiant est constitué de 1 à 9 "
	       . "chiffres. Il s'agit de l'identifiant qui vous a été "
	       . "communiqué par Paybox.";
    }
    return true;
}

function get_identifiant() {
    return($this->identifiant);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_TXT
////////////////////////////////////////////////////////////////
function set_txt($txt) {
    $lerreur = Paybox::check_txt($txt);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_TXT', $lerreur);
        return;
    }
    $this->txt = $txt;
}

function check_txt($erreur) {
    return true;
}

function get_txt() {
    return($this->txt);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_WAIT
////////////////////////////////////////////////////////////////
function set_wait($wait) {
    $lerreur = Paybox::check_wait($wait);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_WAIT', $lerreur);
        return;
    }
    $this->wait = $wait;
}

function check_wait($wait) {
    if (5 < strlen($wait)) {
        return "La durée d'attente du formulaire est trop longue. Elle ne "
               . "doit pas excéder 5 caractères.";
    }
    if (preg_match('/[^0-9]/is', $wait)) {
        return "IBS_WAIT: La durée ne doit comporter que des chiffres.";
    }
    return true;
}

function get_wait() {
    return($this->wait);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_BOUTPI
////////////////////////////////////////////////////////////////
function set_boutpi($boutpi) {
    $lerreur = Paybox::check_boutpi($boutpi);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_BOUTPI', $lerreur);
        return;
    }
    $this->boutpi = $boutpi;
}

function check_boutpi($boutpi) {
    if (50 < strlen($boutpi)) {
        return "Le message du bouton du formulaire est trop long. Il ne doit "
               . "pas excéder 50 caractères.";
    }
    return true;
}

function get_boutpi() {
    return($this->boutpi);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_BKGD
////////////////////////////////////////////////////////////////
function set_bkgd($bkgd) {
    $lerreur = Paybox::check_bkgd($bkgd);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_BKGD', $lerreur);
        return;
    }
    $this->bkgd = $bkgd;
}

function check_bkgd($bkgd) {
    if (150 < strlen($bkgd)) {
        return "Le nom de la couleur de fond du formulaire est trop long. "
               . "Il ne doit pas excéder 150 caractères.";
    }
    return true;
}

function get_bkgd() {
    return($this->bkgd);
}

////////////////////////////////////////////////////////////////
// Gestion de la variable IBS_DATE
////////////////////////////////////////////////////////////////
function set_date($date) {
    $lerreur = Paybox::check_date($date);
    if (is_string($lerreur)) {
        $this->paybox_erreur('IBS_DATE', $lerreur);
        return;
    }
    // Date au format JJ/MM/AAAA HH:MM:SS
    $this->date = $date;
}

function check_date($date) {
    if (preg_match('/^\d\d\/\d\d\/\d\d\d\d \d\d:\d\d:\d\d$/is', $date)) {
        return "IBS_DATE: le format est incorrect. Le format attendu "
               . "est: 'JJ/MM/AAAA hh:mm:ss'.";
    }
    return true;
}

function get_date() {
    return($this->date);
}

////////////////////////////////////////////////////////////////
// Gestion des erreurs
////////////////////////////////////////////////////////////////
function paybox_erreur($var, $erreur) {
    print "Erreur lors de l'initialisation de la variable $var: $erreur";
    exit;
}


////////////////////////////////////////////////////////////////
// Lancement de la requête vers Paybox
////////////////////////////////////////////////////////////////
function paiement() {
    $ancien = error_reporting(0);
    // on impose le mode ?
    $commande = "PBX_MODE=4 ";
    $site = $this->get_site();
    $erreur = Paybox::check_site($site);
    if (is_string($erreur)) {
        return $erreur;
    }
    $commande .= "PBX_SITE=" . escapeshellarg($site) . " ";
    $rang = $this->get_rang();
    $erreur = Paybox::check_rang($rang);
    if (is_string($erreur)) {
        return $erreur;
    }
    $commande .= "PBX_RANG=" . escapeshellarg($rang) . " ";
    $total = $this->get_total();
    $erreur = Paybox::check_total($total);
    if (is_string($erreur)) {
        return $erreur;
    }
    $commande .= "PBX_TOTAL=" . escapeshellarg($total) . " ";
    $cmd = $this->get_cmd();
    $erreur = Paybox::check_cmd($cmd);
    if (is_string($erreur)) {
        return $erreur;
    }
    $devise = $this->get_devise();
    if ('' != $devise) {
        $erreur = Paybox::check_devise($devise);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_DEVISE=$devise ";
    }
    $identifiant = $this->get_identifiant();
    if ('' != $identifiant) {
        $erreur = Paybox::check_identifiant($identifiant);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_IDENTIFIANT=$identifiant ";
    }
    $commande .= "PBX_CMD=" . escapeshellarg($cmd) . " ";
    $porteur = $this->get_porteur();
    $erreur = Paybox::check_porteur($porteur);
    if (is_string($erreur)) {
        return $erreur;
    }
    $commande .= "PBX_PORTEUR=\"$porteur\" ";
    $effectue = $this->get_effectue();
    if ('' != $effectue) {
        $erreur = Paybox::check_effectue($effectue);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_EFFECTUE=" . escapeshellarg($effectue) . " ";
    }
    $refuse = $this->get_refuse();
    if ('' != $refuse) {
        $erreur = Paybox::check_refuse($refuse);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_REFUSE=" . escapeshellarg($refuse) . " ";
    }
    $annule = $this->get_annule();
    if ('' != $annule) {
        $erreur = Paybox::check_annule($annule);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_ANNULE=" . escapeshellarg($annule) . " ";
    }
    $opt = $this->get_output();
    if ('' != $opt) {
        $erreur = Paybox::check_output($opt);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_OPT=" . escapeshellarg($opt) . " ";
    }

    $txt = $this->get_txt();
    if ('' != $txt) {
        $erreur = Paybox::check_txt($txt);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_TXT=" . escapeshellarg($txt) . " ";
    }
    $wait = $this->get_wait();
    if ('' != $wait) {
        $erreur = Paybox::check_wait($wait);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_WAIT=" . escapeshellarg($wait) . " ";
    }
    $boutpi = $this->get_boutpi();
    if ('' != $boutpi) {
        $erreur = Paybox::check_boutpi($boutpi);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_BOUTPI=" . escapeshellarg($boutpi) . " ";
    }
    $bkgd = $this->get_bkgd();
    if ('' != $bkgd) {
        $erreur = Paybox::check_bkgd($bkgd);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_BKGD=" . escapeshellarg($bkgd) . " ";
    }
    $erreur = $this->get_erreur();
    if ('' != $erreur) {
        $lerreur = Paybox::check_erreur($erreur);
        if (is_string($lerreur)) {
            return $lerreur;
        }
        $commande .= "PBX_ERREUR=" . escapeshellarg($erreur) . " ";
    }
    $langue = $this->get_langue();
    if ('' != $langue) {
        $erreur = Paybox::check_langue($langue);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_LANGUE=" . escapeshellarg($langue) . " ";
    }
    $date = $this->get_date();
    if ('' != $date) {
        $erreur = Paybox::check_date($date);
        if (is_string($erreur)) {
            return $erreur;
        }
        $commande .= "PBX_DATE=" . escapeshellarg($date) . " ";
    }

// ca finalise la commande...
    $commande .= 'IBS_RETOUR="total:M;cmd:R;autorisation:A;transaction:T" ';
    // ajouter l'operateur @, car on ne veut aucun rapport d'erreur
    $exe = ('' == $this->identifiant) ? 'paybox' : 'payboxV2';
    $x = shell_exec('/usr/php_exec_dir/' . $exe . ' ' . $commande);
    $return = trim(preg_replace('/^.*?<html>/is', '<html>', $x));
    $error_level = error_reporting($ancien);
    return $return;
} // paiement

} // class paybox
?>
