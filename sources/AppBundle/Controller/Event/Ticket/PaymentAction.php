<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Payment\PayboxFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

final class PaymentAction extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly Emails $emails,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly PayboxFactory $payboxFactory,
        private readonly EventActionHelper $eventActionHelper,
        private readonly Facturation $facturation,
    ) {}

    public function __invoke($eventSlug, Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $invoiceRepository = $this->invoiceRepository;

        $invoiceRef = $request->get('invoiceRef', $request->query->get('invoiceRef', null));

        $invoice = $invoiceRepository->getByReference($invoiceRef);

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('Could not find invoice with reference "%s"', $invoiceRef));
        }

        if ($invoice->getStatus() === Ticket::STATUS_PAID) {
            $this->logger->warning(
                sprintf('Invoice %s already paid, cannot show the paymentAction', $invoiceRef),
            );
            return $this->render('event/ticket/payment_already_done.html.twig', ['event' => $event]);
        }

        $amount = $invoice->getAmount();
        if (false === $event->hasPricesDefinedWithVat()) {
            $amount = Vat::getRoundedWithVatPriceFromPriceWithoutVat($amount, Utils::TICKETING_VAT_RATE);
        }


        $params = [
            'event' => $event,
            'invoice' => $invoice,
            'amount' => $amount,
            'tickets' => $this->ticketRepository->getByInvoiceWithDetail(
                $invoice,
            ),
        ];

        if ($invoice->isFree()) {
            $invoice->setStatus(Ticket::STATUS_PAID);
            $invoice->setInvoiceDate(new \DateTime('now'));
            $invoice->setPaymentDate(new \DateTime('now'));
            $invoice->setInvoice(true);
            $invoice->setPaymentType(Ticket::PAYMENT_NONE);

            $invoiceRepository->save($invoice);

            $this->facturation->envoyerFacture($invoice->getReference());

            $ticketRepository = $this->ticketRepository;
            $tickets = $ticketRepository->getByInvoiceWithDetail($invoice);

            /** @var Ticket $ticket */
            foreach ($tickets as $ticket) {
                $ticket->setStatus(Ticket::STATUS_PAID);
                $ticket->setInvoiceStatus(Ticket::INVOICE_SENT);
                $ticketRepository->save($ticket);

                $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                    $this->emails->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                    return 1;
                });
            }
        } elseif ($invoice->getPaymentType() === Ticket::PAYMENT_CREDIT_CARD) {
            $params['paybox'] = $this->payboxFactory->createPayboxForTicket($invoice, $event, $amount);
        } elseif ($invoice->getPaymentType() === Ticket::PAYMENT_BANKWIRE) {
            $bankAccountFactory = new BankAccountFactory();
            $params['bankAccount'] = $bankAccountFactory->createApplyableAt($invoice->getinvoiceDate());

            // For bankwire, companies need to retrieve the invoice
            $this->facturation->envoyerFacture($invoiceRef);
        }

        return $this->render('event/ticket/payment.html.twig', $params);
    }
}
