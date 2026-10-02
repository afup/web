<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\SponsorTicket;

class TicketFactory
{
    public function createTicketFromSponsorTicket(SponsorTicket $sponsorTicket): Ticket
    {
        $ticket = new Ticket();
        $ticket
            ->setForumId($sponsorTicket->eventId)
            ->setAmount(0)
            ->setCompanyCitation(true)
            ->setReference('SPONSOR-' . $sponsorTicket->eventId . '-' . $sponsorTicket->id)
            ->setInvoiceStatus(Ticket::INVOICE_SENT)
            ->setStatus(Ticket::STATUS_PAID)
            ->setTicketTypeId(Ticket::TYPE_SPONSOR)
            ->setDate(new \DateTime())
        ;

        return $ticket;
    }
}
