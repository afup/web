<?php

namespace AppBundle\Event;

use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\Ting\Exception;

/**
 * Class Sponsor
 * @package AppBundle\Event
 * @todo trouver un meilleur nom
 */
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

    public function __construct (
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
            $this->ticketRepository->save($ticket);

            $sponsorTicket->setUsedInvitations($sponsorTicket->getUsedInvitations()+1);
            $this->sponsorTicketRepository->save($sponsorTicket);
            $this->invoiceRepository->commit();
        } catch (Exception $e) {
            $this->invoiceRepository->rollback();
        }
    }

    public function getRegisteredTickets(SponsorTicket $sponsorTicket)
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        return $this->ticketRepository->getByReference($invoice->getReference());
    }
}
