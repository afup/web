<?php
function generateMultipass($data, $site_key, $api_key) {
    $salted = $api_key . $site_key;
    $hash = hash('sha1', $salted, true);
    $saltedHash = substr($hash, 0, 16);
    $iv = "PHPRedmineConnec";
    // double XOR first block
    for ($i = 0; $i < 16; $i++)
    {
        $data[$i] = $data[$i] ^ $iv[$i];
    }
    $pad = 16 - (strlen($data) % 16);
    $data = $data . str_repeat(chr($pad), $pad);
    $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
    mcrypt_generic_init($cipher, $saltedHash, $iv);
    $encryptedData = mcrypt_generic($cipher, $data);
    mcrypt_generic_deinit($cipher);
    return urlencode(base64_encode($encryptedData));
}

$configuration = $GLOBALS['AFUP_CONF'];

$date = new DateTime();
$date->add(new DateInterval('P1D'));
$expiry = $date->format('Y-m-d H:i:s');

$login_string = json_encode(array('remote_uid' =>  4 /*$droits->obtenirIdentifiant()*/, 'expires' => $expiry . 'Z'));
$encoded_login_string = generateMultipass($login_string,
                                          $configuration->obtenir('redmine|sitekey'),
                                          $configuration->obtenir('redmine|apikey'));
$lien = 'http://redmine.afup.org/multipass/?back_url=%2Fprojects%2Fafup-web%2Fissues%2Fnew&sso=' . $encoded_login_string;
header('Location: '. $lien);
