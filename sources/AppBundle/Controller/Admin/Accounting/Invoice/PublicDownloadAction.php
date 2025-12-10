<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Invoice;

use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicDownloadAction extends AbstractController
{
    public function __construct(private readonly Facture $facture) {}

    public function __invoke(Request $request): Response
    {
        $invoiceRef = Utils::decryptFromText(urldecode($request->query->get('ref', '')));
        if (!$invoiceRef) {
            throw $this->createNotFoundException('Facture inexistante, ref manquant');
        }
        $invoice = $this->facture->obtenir($invoiceRef);
        if (!$invoice) {
            throw $this->createNotFoundException('Facture inexistante');
        }

        ob_start();
        $this->facture->genererFacture($invoice['numero_facture']);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
