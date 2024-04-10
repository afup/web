<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Event\Form\SponsorScanType;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorScan;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\HttpFoundation\Request;

class SponsorController extends EventBaseController
{
    public function scanAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        /** @var SponsorScanRepository $scanRepository */
        $scanRepository = $this->get('ting')->get(SponsorScanRepository::class);
        $scans = $scanRepository->getBySponsorTicket($sponsorTicket);

        return $this->render(':event/sponsor:scan.html.twig', [
            'event' => $event,
            'sponsorTicket' => $sponsorTicket,
            'scans' => $scans,
        ]);
    }

    public function newScanAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        try {
            $this->checkSponsorTicket($request);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $form = $this->createForm(SponsorScanType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('sponsor_scan_flash', [
                'eventSlug' => $event->getPath(),
                'code' => $data['code'],
            ]);
        }

        return $this->render(':event/sponsor:scan_new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function flashAction(Request $request, string $code, string $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        /** @var TicketRepository $ticketRepository */
        $ticketRepository = $this->get('ting')->get(TicketRepository::class);
        /** @var Ticket $ticket */
        $ticket = $ticketRepository->getOneBy(['forumId' => $event->getId(), 'qrCode' => $code]);

        if ($ticket === null) {
            $this->addFlash('error', 'Code inexistant ou invalide');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        /** @var SponsorScanRepository $scanRepository */
        $scanRepository = $this->get('ting')->get(SponsorScanRepository::class);
        $scan = $scanRepository->getOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'ticketId' => $ticket->getId()]);

        if ($scan instanceof SponsorScan) {
            $this->addFlash('error', 'Code déjà scanné.');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        $scan = (new SponsorScan())
            ->setSponsorTicketId($sponsorTicket->getId())
            ->setTicketId($ticket->getId())
            ->setCreatedOn(new \DateTime('now'))
        ;
        $scanRepository->save($scan);

        $this->addFlash('success', 'QR Code ajouté !');

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }

    private function checkSponsorTicket(Request $request)
    {
        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            throw new \Exception('Merci de renseigner votre token.');
        }

        /**
         * @var SponsorTicket $sponsorTicket
         */
        $sponsorTicket = $this->get('ting')->get(SponsorTicketRepository::class)->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            throw new \Exception('Token invalide.');
        }

        return $sponsorTicket;
    }
}
