<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Action vers laquelle l'utilisateur est redirigé après paiement (ou tentative)
 */
final class PayboxRedirectAction extends AbstractController
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly EventActionHelper $eventActionHelper,
    ) {}

    public function __invoke(string $eventSlug, Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $invoice = $this->invoiceRepository->getByReference($request->get('cmd'));

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $request->get('cmd')));
        }

        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->render('event/ticket/paybox_redirect.html.twig', [
            'event' => $event,
            'invoice' => $invoice,
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status'),
        ]);
    }
}
