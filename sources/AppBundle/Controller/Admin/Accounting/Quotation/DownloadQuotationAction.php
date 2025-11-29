<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Accounting\Quotation;

use Afup\Site\Comptabilite\Facture;
use AppBundle\Accounting\Model\Repository\InvoicingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadQuotationAction extends AbstractController
{
    public function __construct(
        private readonly Facture $facture,
        private readonly InvoicingRepository $invoicingRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $quotationRef = $request->query->get('ref');
        $quotation = $this->invoicingRepository->getOneBy(['quotationNumber' => $quotationRef]);
        if ($quotation === null) {
            throw new NotFoundHttpException("Ce devis n'existe pas");
        }

        ob_start();
        $this->facture->genererDevis($quotationRef);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
