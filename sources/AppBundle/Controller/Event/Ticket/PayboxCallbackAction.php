<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use Afup\Site\Forum\Facturation;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\LegacyModelFactory;
use AppBundle\Payment\PayboxResponse;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Action vers laquelle paybox post les résultats du paiement en serveur à serveur
 */
final class PayboxCallbackAction extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Emails $emails,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly LegacyModelFactory $legacyModelFactory,
        private readonly TicketRepository $ticketRepository,
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

        $paymentStatus = Ticket::STATUS_ERROR;
        $invoiceStatus = Ticket::INVOICE_TODO;
        if ($payboxResponse->isSuccessful()) {
            $paymentStatus = Ticket::STATUS_PAID;
            $invoiceStatus = Ticket::INVOICE_SENT;
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_DUPLICATE) {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_CANCELED) {
            $paymentStatus = Ticket::STATUS_CANCELLED;
        } elseif ($payboxResponse->isErrorCode()) {
            $paymentStatus = Ticket::STATUS_DECLINED;
        }
        $invoice
            ->setStatus($paymentStatus)
            ->setPaymentDate(new \DateTime())
            ->setAuthorization($payboxResponse->getAuthorizationId())
            ->setTransaction($payboxResponse->getTransactionId())
        ;
        $this->invoiceRepository->save($invoice);
        $tickets = $this->ticketRepository->getByReference($invoice->getReference());

        if ($paymentStatus === Ticket::STATUS_PAID) {
            /**
             * @var Facturation $forumFacturation
             */
            $forumFacturation = $this->legacyModelFactory->createObject(Facturation::class);
            $forumFacturation->envoyerFacture($invoice->getReference());
        }

        foreach ($tickets as $ticket) {
            /**
             * @var $ticket Ticket
             */
            $ticket
                ->setStatus($paymentStatus)
                ->setInvoiceStatus($invoiceStatus)
            ;
            $this->ticketRepository->save($ticket);

            if ($paymentStatus === Ticket::STATUS_PAID) {
                $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                    $this->emails->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                    return 1;
                });
            }
        }
        return new Response();
    }
}
