<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorScan;
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

        $scan = $this->sponsorScanRepository->getOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'ticketId' => $ticket->getId()]);

        if ($scan instanceof SponsorScan && $scan->getDeletedOn() === null) {
            $this->addFlash('error', 'Code déjà scanné.');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        if (!$scan instanceof SponsorScan) {
            $scan = (new SponsorScan())
                ->setSponsorTicketId($sponsorTicket->getId())
                ->setTicketId($ticket->getId());
        }

        $scan->setCreatedOn(new \DateTime('now'))
            ->setDeletedOn(null);
        $this->sponsorScanRepository->save($scan);

        $this->addFlash('success', 'QR Code ajouté !');

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }
}
