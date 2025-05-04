<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Exception;

class SponsorTicketHelper
{
    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
    ) {
    }

    public function addTicketToSponsor(SponsorTicket $sponsorTicket, Ticket $ticket): void
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        try {
            $this->invoiceRepository->startTransaction();
            $this->invoiceRepository->save($invoice);

            if ($ticket->getId() === null) {
                // This is a new ticket, so we update the number of tickets created for this sponsor
                $sponsorTicket->setUsedInvitations($sponsorTicket->getUsedInvitations()+1);
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
            $sponsorTicket->setUsedInvitations($sponsorTicket->getUsedInvitations()-1);
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
