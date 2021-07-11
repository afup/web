<?php
use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Utils\SymfonyKernel;
use AppBundle\Payment\PayboxFactory;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

if (
    !isset($_GET['ref'])
    ||
    !(
        preg_match('`ins-([0-9]+)`', $_GET['ref'], $matches)
        ||
        preg_match('`elephpant-([0-9]+)`', $_GET['ref'], $matches)
    )
) {
    die('Missing ref');
}

$forum_inscriptions = new Inscriptions($bdd);
$forum = new Forum($bdd);
$forumId = isset($_GET['forum']) ? intval($_GET['forum']) : $forum->obtenirDernier();
$forumData = $forum->obtenir($forumId);

if (!isset($forumData['id']) || !$forumData['id']) {
    die('Forum not found');
}

$ref = $matches[1];
$inscription = $forum_inscriptions->obtenir($ref);

if (preg_match('`elephpant-([0-9]+)`', $_GET['ref'], $matches)) {
    $prix = 25;
    $action = 'elep';
} else {
    $prix = 100;
    $action = 'subs';
}
if (isset($_GET['prix'])) {
    $prix = intval($_GET['prix']);
}

$symfonyKernel = new SymfonyKernel();

/** @var PayboxFactory $payboxFactory */
$payboxFactory = $symfonyKernel->getKernel()->getContainer()->get(PayboxFactory::class);

$paybox = $payboxFactory->getPaybox();

$paybox
    ->setTotal($prix * 100)
    ->setCmd(strtr($forumData['path'], ['forum' => 'frm', 'phptour' => 'tour']) . '-' . $action . '-' . $ref . '-' . $prix)
    ->setPorteur($inscription['email'])
    ->setUrlRetourEffectue('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_effectue.php')
    ->setUrlRetourRefuse('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_refuse.php')
    ->setUrlRetourAnnule('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_annule.php')
    ->setUrlRetourErreur('https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/paybox_erreur.php')
;

$smarty->assign('paybox', $paybox->generate(new \DateTime()));
$smarty->assign('inscription', $inscription);
$smarty->assign('original_ref', urlencode($_GET['ref']));
$smarty->assign('forum', $forumData);
$smarty->display('paybox_formulaire.html');


// https://afup.org/pages/phptournantes2017/paiement/?ref=FOO
