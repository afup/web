<?php
use Afup\Site\Comptabilite\Facture;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

$comptaFact = new Facture($bdd);

$ref = $comptaFact->decryptLink(str_replace(' ', '+', urldecode($_GET['ref'])));

$facture = $comptaFact->obtenir($ref);
if ($facture) {
    if (isset($_GET['action']) && $_GET['action'] == 'voir-pdf') {
        $comptaFact->genererFacture($facture['numero_facture']);
    } else {
        $details = $comptaFact->obtenir_details($ref);
        $prix = 0;
        foreach ($details as $d) {
            $prix += $d['quantite'] * $d['pu'];
        }

        require_once dirname(__FILE__).'/../../../dependencies/paybox/payboxv2.inc';
        $paybox = new PAYBOX;
        $paybox->set_langue('FRA');
        $paybox->set_site($conf->obtenir('paybox|site'));
        $paybox->set_rang($conf->obtenir('paybox|rang'));
        $paybox->set_identifiant('83166771');

        $paybox->set_total($prix * 100);
        $paybox->set_cmd($facture['numero_facture']);
        $paybox->set_porteur($facture['email']);

        $paybox->set_effectue('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php');
        $paybox->set_refuse('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php');
        $paybox->set_annule('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php');
        $paybox->set_erreur('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php');

        $paybox->set_wait(50000);
        $paybox->set_boutpi('R&eacute;gler par carte');
        $paybox->set_bkgd('#FAEBD7');
        $paybox->set_output('B');

        preg_match('#<CENTER>(.*)</CENTER>#is', $paybox->paiement(), $r);
        $r[1] = preg_replace('#<b>.*?</b>#', '', $r[1]);
        $smarty->assign('paybox', str_ireplace('input type=submit', 'input type="submit" class="btn primary"', $r[1]));
        $smarty->assign('facture', $facture);
        $smarty->assign('details_facture', $details);
        $smarty->assign('original_ref', urlencode($_GET['ref']));
        $smarty->display('paybox_formulaire.html');
    }
} else {
    header('Location: /');
}
// https://afup.org/pages/paiement/index.php?&ref=P2wCCWfF%2BAU%3D
