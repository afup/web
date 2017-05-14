<?php
use Afup\Site\Forum\Inscriptions;
error_reporting(E_ALL);
ini_set('display_errors', 1);
//phpinfo();
//exit;

require_once dirname(__FILE__) .'/../../../../sources/Afup/Bootstrap/Http.php';

if (!isset($_GET['ref']) || !preg_match('`ins-([0-9]+)`', $_GET['ref'], $matches)) {
    die('Missing ref');
}

$forum_inscriptions = new Inscriptions($bdd);
$ref = $matches[1];
$inscription = $forum_inscriptions->obtenir($ref);

$prix = isset($_GET['prix']) ? intval($_GET['prix']) : 100;

require_once dirname(__FILE__).'/../../../../dependencies/paybox/payboxv2.inc';
$paybox = new PAYBOX;
$paybox->set_langue('FRA');
$paybox->set_site($conf->obtenir('paybox|site'));
$paybox->set_rang($conf->obtenir('paybox|rang'));
$paybox->set_identifiant('83166771');

$paybox->set_total($prix * 100);
$paybox->set_cmd('PHPTOUR17-' . $ref);
$paybox->set_porteur($inscription['email']);

$paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php');
$paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php');
$paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php');
$paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php');

$paybox->set_wait(50000);
$paybox->set_boutpi('R&eacute;gler ' . $prix . ' &euro; par carte');
$paybox->set_bkgd('#FAEBD7');
$paybox->set_output('B');

preg_match('#<CENTER>(.*)</CENTER>#is', $paybox->paiement(), $r);
$r[1] = preg_replace('#<b>.*?</b>#', '', $r[1]);
$smarty->assign('paybox', str_ireplace('input type=submit', 'input type="submit" class="btn primary"', $r[1]));
$smarty->assign('inscription', $inscription);
$smarty->assign('original_ref', urlencode($_GET['ref']));
$smarty->display('paybox_formulaire.html');


// http://afup.org/pages/phptournantes2017/paiement/?ref=FOO
