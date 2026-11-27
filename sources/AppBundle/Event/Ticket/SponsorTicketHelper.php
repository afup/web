<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\InvoiceFactory;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Entity\Inscription;
use CCMBenchmark\Ting\Exception;
use Doctrine\DBAL\Exception as DBALException;

class SponsorTicketHelper
{
    public function __construct(
        private readonly InvoiceFactory $invoiceFactory,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
    ) {}

    public function addTicketToSponsor(SponsorTicket $sponsorTicket, Inscription $ticket, bool $isNewTicket): void
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);

        if ($isNewTicket) {
            // Réservée avant l'enregistrement de l'inscription :
            // en cas d'échec entre les écritures, le compteur surestime, sans risque de dépasser le quota
            $sponsorTicket->usedInvitations++;
            $this->sponsorTicketRepository->save($sponsorTicket);
        }

        // La facture (Ting) et l'inscription (Doctrine) n'utilisent pas la même connexion :
        // la facture sponsor est partagée par toutes les inscriptions du token, elle n'est donc pas supprimée en cas d'échec
        try {
            $this->invoiceRepository->save($invoice);
            $this->inscriptionRepository->save($ticket);
        } catch (Exception|DBALException) {
            if ($isNewTicket) {
                $sponsorTicket->usedInvitations--;
                $this->sponsorTicketRepository->save($sponsorTicket);
            }
        }
    }

    public function removeTicketFromSponsor(SponsorTicket $sponsorTicket, Inscription $ticket): void
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        if ($invoice->getReference() !== $ticket->reference) {
            throw new \RuntimeException('Erreur: le ticket n\'est pas rattaché à ce token');
        }
        try {
            $this->inscriptionRepository->delete($ticket);
        } catch (DBALException) {
            return;
        }

        // Libérée seulement après la suppression de l'inscription :
        // si cette écriture échoue, le compteur surestime, sans risque de dépasser le quota
        $sponsorTicket->usedInvitations--;
        $this->sponsorTicketRepository->save($sponsorTicket);
    }

    public function doesTicketBelongsToSponsor(SponsorTicket $sponsorTicket, Inscription $ticket): bool
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        return $ticket->reference === $invoice->getReference();
    }

    /**
     * @return list<Inscription>
     */
    public function getRegisteredTickets(SponsorTicket $sponsorTicket): array
    {
        $invoice = $this->invoiceFactory->createInvoiceFromSponsorTicket($sponsorTicket);
        return $this->inscriptionRepository->getByReference((string) $invoice->getReference());
    }
}
