<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\TypeReglement;

class InvoiceFactory
{
    public function __construct(private readonly InvoiceRepository $invoiceRepository) {}

    public function createInvoiceFromSponsorTicket(SponsorTicket $sponsorTicket): Invoice
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
            ->setPaymentType(TypeReglement::Aucun->value)
            ->setEmail('bureau@afup.org')
            ->setAddress('N/A')
            ->setZipcode('N/A')
            ->setCity('N/A')
            ->setCountryId('FR')
            ->setStatus(InscriptionEtat::Invitation->value)
            ->setInvoice(false)
        ;

        return $invoice;
    }

    public function createInvoiceForEvent(Event $event): Invoice
    {
        $invoice = new Invoice();
        $invoice
            ->setForumId($event->getId())
            ->setStatus(InscriptionEtat::Creee->value)
            ->setPaymentType(TypeReglement::CarteBancaire->value)
            ->setInvoice(false)
        ;

        return $invoice;
    }
}
