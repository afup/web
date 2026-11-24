<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;
use AppBundle\Event\Enum\TypeInscription;

class TicketFactory
{
    public function createTicketFromSponsorTicket(SponsorTicket $sponsorTicket): Inscription
    {
        $ticket = new Inscription();
        $ticket->forumId = $sponsorTicket->eventId;
        $ticket->amount = 0.0;
        $ticket->companyCitation = true;
        $ticket->reference = 'SPONSOR-' . $sponsorTicket->eventId . '-' . $sponsorTicket->id;
        $ticket->invoiceStatus = InscriptionFacturation::Envoyee;
        $ticket->status = InscriptionEtat::Reglee;
        $ticket->ticketTypeId = TypeInscription::Sponsor->value;
        $ticket->date = new \DateTime();

        return $ticket;
    }
}
