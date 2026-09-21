<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Entity\Repository\SponsorScanRepository;
use AppBundle\Event\Entity\SponsorScan;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class FlashAction extends SponsorScanController
{
    public function __construct(
        SponsorTicketRepository $sponsorTicketRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorScanRepository $sponsorScanRepository,
        private readonly TicketRepository $ticketRepository,
    ) {
        parent::__construct($sponsorTicketRepository);
    }

    public function __invoke(Request $request, string $code, string $eventSlug): RedirectResponse
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $ticket = $this->ticketRepository->getOneBy(['forumId' => $event->getId(), 'qrCode' => $code]);

        if ($ticket === null) {
            $this->addFlash('error', 'Code inexistant ou invalide');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        $scan = $this->sponsorScanRepository->findOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'ticketId' => $ticket->getId()]);

        if ($scan instanceof SponsorScan && $scan->deletedOn === null) {
            $this->addFlash('error', 'Code déjà scanné.');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        if (!$scan instanceof SponsorScan) {
            $scan = $this->createSponsorScan($sponsorTicket, $ticket);
        }

        $scan->createdOn = new \DateTimeImmutable('now');
        $scan->deletedOn = null;
        $this->sponsorScanRepository->save($scan);

        $this->addFlash('success', 'QR Code ajouté !');

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }

    private function createSponsorScan(SponsorTicket $sponsorTicket, Ticket $ticket): SponsorScan
    {
        $sponsorTicketId = $sponsorTicket->getId();
        $ticketId = $ticket->getId();
        if ($sponsorTicketId === null || $ticketId === null) {
            throw new \InvalidArgumentException('Le ticket sponsor et le billet doivent être enregistrés');
        }

        $scan = new SponsorScan();
        $scan->sponsorTicketId = $sponsorTicketId;
        $scan->ticketId = $ticketId;

        return $scan;
    }
}
