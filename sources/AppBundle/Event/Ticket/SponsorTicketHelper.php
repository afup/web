<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Exception;

class SponsorTicketHelper
{
    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
    ) {}

    public function addTicketToSponsor(SponsorTicket $sponsorTicket, Ticket $ticket): void
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        try {
            $this->invoiceRepository->startTransaction();
            $this->invoiceRepository->save($invoice);

            if ($ticket->getId() === null) {
                // Nouveau ticket : on incrémente le compteur d'invitations utilisées du sponsor
                $sponsorTicket->usedInvitations++;
            }

            $this->ticketRepository->save($ticket);
            $this->sponsorTicketRepository->save($sponsorTicket);
            $this->invoiceRepository->commit();
        } catch (Exception) {
            $this->invoiceRepository->rollback();
        }
    }

    public function removeTicketFromSponsor(SponsorTicket $sponsorTicket, Ticket $ticket): void
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        if ($invoice->getReference() !== $ticket->getReference()) {
            throw new \RuntimeException('Erreur: le ticket n\'est pas rattaché à ce token');
        }
        try {
            $this->ticketRepository->startTransaction();
            $this->ticketRepository->delete($ticket);
            $sponsorTicket->usedInvitations--;
            $this->sponsorTicketRepository->save($sponsorTicket);
            $this->ticketRepository->commit();
        } catch (Exception) {
            $this->ticketRepository->rollback();
        }
    }

    public function doesTicketBelongsToSponsor(SponsorTicket $sponsorTicket, Ticket $ticket): bool
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        return ($ticket->getReference() === $invoice->getReference());
    }

    public function getRegisteredTickets(SponsorTicket $sponsorTicket)
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        return $this->ticketRepository->getByReference($invoice->getReference());
    }
}
