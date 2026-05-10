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

class DownloadDevisAction extends AbstractController
{
    public function __construct(
        private readonly Facturation $facturation,
        private readonly InvoiceRepository $invoiceRepository,
    ) {}

    public function __invoke(Request $request): Response
    {
        $reference = $request->query->get('ref');
        $devis = $this->invoiceRepository->getByReference($reference);
        if (!$devis instanceof Invoice) {
            throw new NotFoundHttpException("Ce devis n'existe pas");
        }

        ob_start();
        $this->facturation->genererDevis($reference);
        $pdf = ob_get_clean();

        $response = new Response($pdf);
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }
}
