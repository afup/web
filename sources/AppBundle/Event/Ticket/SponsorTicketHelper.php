<?php

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
    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var TicketRepository
     */
    private $ticketRepository;

    /**
     * @var SponsorTicketRepository
     */
    private $sponsorTicketRepository;

    public function __construct(
        InvoiceFactory $invoiceFactory,
        InvoiceRepository $invoiceRepository,
        TicketRepository $ticketRepository,
        SponsorTicketRepository $sponsorTicketRepository
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->ticketRepository = $ticketRepository;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
    }

    public function addTicketToSponsor(SponsorTicket $sponsorTicket, Ticket $ticket)
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
        } catch (Exception $e) {
            $this->invoiceRepository->rollback();
        }
    }

    public function removeTicketFromSponsor(SponsorTicket $sponsorTicket, Ticket $ticket)
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        if ($invoice->getReference() !== $ticket->getReference()) {
            throw new \RuntimeException(sprintf('Erreur: le ticket n\'est pas rattaché à ce token'));
        }
        try {
            $this->ticketRepository->startTransaction();
            $this->ticketRepository->delete($ticket);
            $sponsorTicket->setUsedInvitations($sponsorTicket->getUsedInvitations()-1);
            $this->sponsorTicketRepository->save($sponsorTicket);
            $this->ticketRepository->commit();
        } catch (Exception $e) {
            $this->ticketRepository->rollback();
        }
    }

    public function doesTicketBelongsToSponsor(SponsorTicket $sponsorTicket, Ticket $ticket)
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
