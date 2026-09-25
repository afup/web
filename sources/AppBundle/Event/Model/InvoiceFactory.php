<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Model\Repository\InvoiceRepository;

class InvoiceFactory
{
    public function __construct(private readonly InvoiceRepository $invoiceRepository) {}

    public function createInvoiceFromSponsorTicket(SponsorTicket $sponsorTicket)
    {
        $reference = 'SPONSOR-' . $sponsorTicket->eventId . '-' . $sponsorTicket->id;
        $invoice = $this->invoiceRepository->get($reference);
        if ($invoice !== null) {
            return $invoice;
        }

        $invoice = new Invoice();
        $invoice
            ->setForumId($sponsorTicket->eventId)
            ->setAmount(0)
            ->setReference($reference)
            ->setCompany($sponsorTicket->societe)
            ->setPaymentType(Ticket::PAYMENT_NONE)
            ->setEmail('bureau@afup.org')
            ->setAddress('N/A')
            ->setZipcode('N/A')
            ->setCity('N/A')
            ->setCountryId('FR')
            ->setStatus(Ticket::STATUS_GUEST)
            ->setInvoice(false)
        ;

        return $invoice;
    }

    public function createInvoiceForEvent(Event $event): Invoice
    {
        $invoice = new Invoice();
        $invoice
            ->setForumId($event->getId())
            ->setStatus(Ticket::STATUS_CREATED)
            ->setPaymentType(Ticket::PAYMENT_CREDIT_CARD)
            ->setInvoice(false)
        ;

        return $invoice;
    }
}
