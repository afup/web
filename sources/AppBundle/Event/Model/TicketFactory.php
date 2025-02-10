<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

class TicketFactory
{
    public function createTicketFromSponsorTicket(SponsorTicket $sponsorTicket): Ticket
    {
        $ticket = new Ticket();
        $ticket
            ->setForumId($sponsorTicket->getIdForum())
            ->setAmount(0)
            ->setCompanyCitation(true)
            ->setReference('SPONSOR-' . $sponsorTicket->getIdForum() . '-' . $sponsorTicket->getId())
            ->setInvoiceStatus(Ticket::INVOICE_SENT)
            ->setStatus(Ticket::STATUS_PAID)
            ->setTicketTypeId(Ticket::TYPE_SPONSOR)
            ->setDate(new \DateTime())
        ;

        return $ticket;
    }
}
