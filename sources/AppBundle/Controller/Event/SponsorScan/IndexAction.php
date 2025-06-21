<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class IndexAction extends SponsorScanController
{
    public function __construct(
        SponsorTicketRepository $sponsorTicketRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorScanRepository $sponsorScanRepository,
    ) {
        parent::__construct($sponsorTicketRepository);
    }

    public function __invoke(Request $request, string $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $scans = $this->sponsorScanRepository->getBySponsorTicket($sponsorTicket);

        return $this->render('event/sponsor/scan.html.twig', [
            'event' => $event,
            'sponsorTicket' => $sponsorTicket,
            'scans' => $scans,
        ]);
    }
}
