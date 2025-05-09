<?php

declare(strict_types=1);

namespace AppBundle\Event\Invoice;

use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    public function handleInvoicing(
        $reference,
        $paymentType,
        $paymentInfos,
        ?\DateTime $paymentDate,
        $email,
        $company,
        $lastname,
        $firstname,
        $address,
        $zipcode,
        $city,
        $countryId,
        $eventId,
        $oldReference = null,
        $authorization = null,
        $transaction = null,
        $status = Ticket::STATUS_CREATED,
    ): void {
        $tickets = $this->ticketRepository->getByReference($reference);
        $amount = 0.0;
        foreach ($tickets as $ticket) {
            $amount = round($amount + $ticket->getAmount(), 2);
        }
        $nbTickets = count($tickets);
        $oldInvoice = null;
        if ($oldReference === '') {
            $oldReference = null;
        }
        if (null !== $oldReference) {
            $oldInvoice = $this->invoiceRepository->getByReference($oldReference);
        }
        // Si la reference n'existe pas on l'ajoute sinon on la met à jour...
        if ($nbTickets === 0 || null === $oldInvoice) {
            $invoice = new Invoice();
            $invoice->setReference($reference);
            $invoice->setInvoice(false);
        } else {
            $invoice = $oldInvoice;
            $invoice->setInvoice(true);
        }
        $invoice->setAmount($amount);
        $invoice->setPaymentType($paymentType);
        $invoice->setPaymentInfos($paymentInfos);
        $invoice->setPaymentDate($paymentDate);
        $invoice->setCompany($company);
        $invoice->setLastname($lastname);
        $invoice->setFirstname($firstname);
        $invoice->setEmail($email);
        $invoice->setAddress($address);
        $invoice->setZipcode($zipcode);
        $invoice->setCity($city);
        $invoice->setCountryId($countryId);
        $invoice->setAuthorization($authorization);
        $invoice->setTransaction($transaction);
        $invoice->setStatus($status);
        $invoice->setForumId($eventId);
        $this->invoiceRepository->save($invoice);
        // Si on change de reference
        if (null !== $oldInvoice && $oldReference !== $reference) {
            $this->deleteInvoice($oldInvoice);
        }
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        $tickets = $this->ticketRepository->getByReference($invoice->getReference());
        if (0 === count($tickets)) {
            $this->invoiceRepository->delete($invoice);

            return;
        }
        $amount = 0.0;
        foreach ($tickets as $ticket) {
            $amount = round($amount + $ticket->getAmount(), 2);
        }
        $invoice->setAmount($amount);
        $this->invoiceRepository->save($invoice);
    }
}
