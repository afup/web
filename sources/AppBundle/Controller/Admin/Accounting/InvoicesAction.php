<?php

namespace AppBundle\Controller\Admin\Accounting;

use Afup\Site\Comptabilite\Facture;
use AppBundle\Controller\Admin\BackOfficeLegacyBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class InvoicesAction
{
    /** @var Twig_Environment */
    private $twig;
    /** @var BackOfficeLegacyBridge */
    private $backOfficeLegacyBridge;

    public function __construct(
        Twig_Environment $twig,
        BackOfficeLegacyBridge $backOfficeLegacyBridge
    ) {
        $this->twig = $twig;
        $this->backOfficeLegacyBridge = $backOfficeLegacyBridge;
    }

    public function __invoke(Request $request)
    {
        global $bdd;
        $response = $this->backOfficeLegacyBridge->handlePage('compta_facture');
        if (null !== $response) {
            return $response;
        }
        $comptaFact = new Facture($bdd);
        $ecritures = $comptaFact->obtenirFacture();
        foreach ($ecritures as &$e) {
            $e['link'] = urlencode(base64_encode(mcrypt_cbc(MCRYPT_TripleDES, 'PaiementFactureAFUP_AFUP', $e['id'], MCRYPT_ENCRYPT, '@PaiFact')));
        }

        return new Response($this->twig->render('admin/accounting/invoices.html.twig', ['ecritures' => $ecritures]));
    }
}
