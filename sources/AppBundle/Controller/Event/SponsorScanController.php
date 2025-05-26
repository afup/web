<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Form\SponsorScanType;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorScan;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SponsorScanController extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorScanRepository $sponsorScanRepository,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
        private readonly TicketRepository $ticketRepository,
    ) {}

    public function index(Request $request, $eventSlug)
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

    public function new(Request $request, $eventSlug)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        try {
            $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
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

        return $this->render('event/sponsor/scan_new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function flash(Request $request, string $code, string $eventSlug): RedirectResponse
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

    public function export(Request $request, $eventSlug)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);
        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $baseName = sprintf('afup_export_qr_codes_%s_%s', $eventSlug, $event->getDateStart()->format('Y'));
        $tmpFile = tempnam(sys_get_temp_dir(), $baseName);
        $file = new \SplFileObject($tmpFile, 'w');

        $scans = $this->sponsorScanRepository->getBySponsorTicket($sponsorTicket);

        $file->fputcsv(['Nom', 'Prénom', 'Email', 'Date']);

        foreach ($scans as $scan) {
            $file->fputcsv([
                $scan['nom'],
                $scan['prenom'],
                $scan['email'],
                $scan['created_on'],
            ]);
        }

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $baseName . '.csv');
        $response->deleteFileAfterSend(true);

        return $response;
    }

    public function delete(Request $request, string $eventSlug, $scanId): RedirectResponse
    {
        $this->eventActionHelper->getEvent($eventSlug);
        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $scan = $this->sponsorScanRepository->getOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'id' => $scanId]);

        if ($scan instanceof SponsorScan) {
            $scan->setDeletedOn(new \DateTime('now'));
            $this->sponsorScanRepository->save($scan);
            $this->addFlash('success', "QR Code supprimé !");
        }

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }

    private function checkSponsorTicket(Request $request)
    {
        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            throw new InvalidSponsorTokenException('Merci de renseigner votre token.');
        }

        $sponsorTicket = $this->sponsorTicketRepository->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            throw new InvalidSponsorTokenException('Token invalide.');
        }

        if (!$sponsorTicket->getQrCodesScannerAvailable()) {
            throw new InvalidSponsorTokenException('Accès non autorisé.');
        }

        return $sponsorTicket;
    }
}
