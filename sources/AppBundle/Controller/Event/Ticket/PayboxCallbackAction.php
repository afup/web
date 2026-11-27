<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Invoice\EventInvoiceMailer;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Payment\PayboxResponse;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;

/**
 * Action vers laquelle paybox post les résultats du paiement en serveur à serveur
 */
final class PayboxCallbackAction extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Emails $emails,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventInvoiceMailer $invoiceMailer,
    ) {}

    public function __invoke(string $eventSlug, Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $invoice = $this->invoiceRepository->getByReference($request->query->get('cmd'));

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $request->query->get('cmd')));
        }

        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        $paymentStatus = InscriptionEtat::ErreurPaiement;
        $invoiceStatus = InscriptionFacturation::AEnvoyer;
        if ($payboxResponse->isSuccessful()) {
            $paymentStatus = InscriptionEtat::Reglee;
            $invoiceStatus = InscriptionFacturation::Envoyee;
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_DUPLICATE) {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_CANCELED) {
            $paymentStatus = InscriptionEtat::Annulee;
        } elseif ($payboxResponse->isErrorCode()) {
            $paymentStatus = InscriptionEtat::PaiementRefuse;
        }
        $invoice
            ->setStatus($paymentStatus->value)
            ->setPaymentDate(new \DateTime())
            ->setAuthorization($payboxResponse->getAuthorizationId())
            ->setTransaction($payboxResponse->getTransactionId())
        ;
        $this->invoiceRepository->save($invoice);
        $tickets = $this->inscriptionRepository->getByReference((string) $invoice->getReference());

        if ($paymentStatus === InscriptionEtat::Reglee) {
            $this->invoiceMailer->send($invoice->getReference());
        }

        foreach ($tickets as $ticket) {
            $ticket->status = $paymentStatus;
            $ticket->invoiceStatus = $invoiceStatus;
            $this->inscriptionRepository->save($ticket);

            if ($paymentStatus === InscriptionEtat::Reglee) {
                $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                    $this->emails->sendInscription($event, new MailUser($ticket->email, $ticket->getLabel()));
                    return 1;
                });
            }
        }
        return new Response();
    }
}
