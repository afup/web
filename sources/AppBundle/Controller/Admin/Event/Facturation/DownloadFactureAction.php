<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Facturation;

use Afup\Site\Forum\Facturation;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadFactureAction extends AbstractController
{
    public function __construct(
        private readonly Facturation $facturation,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $facture = $this->invoiceRepository->getByReference($reference);
        if (!$facture instanceof Invoice) {
            throw new NotFoundHttpException("Cette facture n'existe pas");
        }

        ob_start();
        $this->facturation->genererFacture($reference);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
