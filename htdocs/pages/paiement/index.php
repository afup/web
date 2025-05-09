<?php

declare(strict_types=1);
use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\SymfonyKernel;
use Afup\Site\Utils\Utils;
use AppBundle\Payment\PayboxBilling;
use AppBundle\Payment\PayboxFactory;

require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';

$comptaFact = new Facture($bdd);

$ref = Utils::decryptFromText(urldecode((string) $_GET['ref']));

$facture = $comptaFact->obtenir($ref);
if ($facture) {
    if (isset($_GET['action']) && $_GET['action'] == 'voir-pdf') {
        $comptaFact->genererFacture($facture['numero_facture']);
    } else {
        $details = $comptaFact->obtenir_details($ref);
        $prix = 0;
        foreach ($details as $d) {
            $prix += $d['quantite'] * $d['pu'] * (1 + ($d['tva'] / 100));
        }

        $symfonyKernel = new SymfonyKernel();

        /** @var PayboxFactory $payboxFactory */
        $payboxFactory = $symfonyKernel->getKernel()->getContainer()->get(PayboxFactory::class);

        $paybox = $payboxFactory->getPaybox();

        $paybox
            ->setTotal($prix * 100)
            ->setCmd($facture['numero_facture'])
            ->setPorteur($facture['email'])
            ->setUrlRetourEffectue('https://' . $_SERVER['HTTP_HOST'] . dirname((string) $_SERVER['REQUEST_URI']) . '/paybox_effectue.php')
            ->setUrlRetourAnnule('https://' . $_SERVER['HTTP_HOST'] . dirname((string) $_SERVER['REQUEST_URI']) . '/paybox_annule.php')
            ->setUrlRetourRefuse('https://' . $_SERVER['HTTP_HOST'] . dirname((string) $_SERVER['REQUEST_URI']) . '/paybox_refuse.php')
            ->setUrlRetourErreur('https://' . $_SERVER['HTTP_HOST'] . dirname((string) $_SERVER['REQUEST_URI']) . '/paybox_erreur.php')
        ;

        $payboxBilling = new PayboxBilling($facture['prenom'], $facture['nom'], $facture['adresse'], $facture['code_postal'], $facture['ville'], $facture['id_pays']);

        $now = new \DateTime();
        $smarty->assign('paybox', $paybox->generate($now, $payboxBilling));

        $smarty->assign('facture', $facture);
        $smarty->assign('details_facture', $details);
        $smarty->assign('original_ref', urlencode((string) $_GET['ref']));
        $smarty->display('paybox_formulaire.html');
    }
} else {
    header('Location: /');
}
// https://afup.org/pages/paiement/index.php?&ref=P2wCCWfF%2BAU%3D
