<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Entity\Repository\SponsorScanRepository;
use AppBundle\Event\Entity\SponsorScan;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class DeleteAction extends SponsorScanController
{
    public function __construct(
        SponsorTicketRepository $sponsorTicketRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorScanRepository $sponsorScanRepository,
    ) {
        parent::__construct($sponsorTicketRepository);
    }

    public function __invoke(Request $request, string $eventSlug, string $scanId): RedirectResponse
    {
        $this->eventActionHelper->getEvent($eventSlug);
        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $scan = $this->sponsorScanRepository->findOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'id' => (int) $scanId]);

        if ($scan instanceof SponsorScan) {
            $scan->deletedOn = new \DateTimeImmutable('now');
            $this->sponsorScanRepository->save($scan);
            $this->addFlash('success', "QR Code supprimé !");
        }

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }
}
