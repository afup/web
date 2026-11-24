<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use Afup\Site\Utils\Utils;
use AppBundle\Event\Invoice\EventInvoiceMailer;
use Afup\Site\Utils\Vat;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Payment\PayboxFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;
use AppBundle\Event\Enum\TypeReglement;

final class PaymentAction extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly Emails $emails,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly PayboxFactory $payboxFactory,
        private readonly EventActionHelper $eventActionHelper,
        private readonly EventInvoiceMailer $invoiceMailer,
    ) {}

    public function __invoke($eventSlug, Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        $invoiceRepository = $this->invoiceRepository;

        $invoiceRef = (string) $request->request->get('invoiceRef', $request->query->get('invoiceRef'));

        $invoice = $invoiceRepository->getByReference($invoiceRef);

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('Could not find invoice with reference "%s"', $invoiceRef));
        }

        if ($invoice->getStatus() === InscriptionEtat::Reglee->value) {
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
            'tickets' => $this->inscriptionRepository->getByReferenceWithTicketEventType($invoiceRef),
        ];

        if ($invoice->isFree()) {
            $invoice->setStatus(InscriptionEtat::Reglee->value);
            $invoice->setInvoiceDate(new \DateTime('now'));
            $invoice->setPaymentDate(new \DateTime('now'));
            $invoice->setInvoice(true);
            $invoice->setPaymentType(TypeReglement::Aucun->value);

            $invoiceRepository->save($invoice);

            $this->invoiceMailer->send($invoice->getReference());

            $tickets = $this->inscriptionRepository->getByReferenceWithTicketEventType($invoiceRef);

            foreach ($tickets as $ticket) {
                $ticket->status = InscriptionEtat::Reglee;
                $ticket->invoiceStatus = InscriptionFacturation::Envoyee;
                $this->inscriptionRepository->save($ticket);

                $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                    $this->emails->sendInscription($event, new MailUser($ticket->email, $ticket->getLabel()));
                    return 1;
                });
            }
        } elseif ($invoice->getPaymentType() === TypeReglement::CarteBancaire->value) {
            $params['paybox'] = $this->payboxFactory->createPayboxForTicket($invoice, $event, $amount);
        } elseif ($invoice->getPaymentType() === TypeReglement::Virement->value) {
            $bankAccountFactory = new BankAccountFactory();
            $params['bankAccount'] = $bankAccountFactory->createApplyableAt($invoice->getinvoiceDate());

            // For bankwire, companies need to retrieve the invoice
            $this->invoiceMailer->send($invoiceRef);
        }

        return $this->render('event/ticket/payment.html.twig', $params);
    }
}
