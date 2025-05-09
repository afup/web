<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event;

use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Form\SponsorScanType;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorScan;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use CCMBenchmark\TingBundle\Repository\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SponsorScanController extends AbstractController
{
    public function __construct(
        private readonly RepositoryFactory $repositoryFactory,
        private readonly EventActionHelper $eventActionHelper,
    ) {
    }
    public function index(Request $request, $eventSlug)
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        try {
            $sponsorTicket = $this->checkSponsorTicket($request);
        } catch (InvalidSponsorTokenException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        /** @var SponsorScanRepository $scanRepository */
        $scanRepository = $this->repositoryFactory->get(SponsorScanRepository::class);
        $scans = $scanRepository->getBySponsorTicket($sponsorTicket);

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

        /** @var TicketRepository $ticketRepository */
        $ticketRepository = $this->repositoryFactory->get(TicketRepository::class);
        /** @var Ticket $ticket */
        $ticket = $ticketRepository->getOneBy(['forumId' => $event->getId(), 'qrCode' => $code]);

        if ($ticket === null) {
            $this->addFlash('error', 'Code inexistant ou invalide');
            return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
        }

        /** @var SponsorScanRepository $scanRepository */
        $scanRepository = $this->repositoryFactory->get(SponsorScanRepository::class);
        $scan = $scanRepository->getOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'ticketId' => $ticket->getId()]);

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
        $scanRepository->save($scan);

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

        $scanRepository = $this->repositoryFactory->get(SponsorScanRepository::class);
        $scans = $scanRepository->getBySponsorTicket($sponsorTicket);

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

        /** @var SponsorScanRepository $scanRepository */
        $scanRepository = $this->repositoryFactory->get(SponsorScanRepository::class);
        $scan = $scanRepository->getOneBy(['sponsorTicketId' => $sponsorTicket->getId(), 'id' => $scanId]);

        if ($scan instanceof SponsorScan) {
            $scan->setDeletedOn(new \DateTime('now'));
            $scanRepository->save($scan);
            $this->addFlash('success', "QR Code supprimé !");
        }

        return $this->redirectToRoute('sponsor_scan', ['eventSlug' => $eventSlug]);
    }

    private function checkSponsorTicket(Request $request)
    {
        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            throw new InvalidSponsorTokenException('Merci de renseigner votre token.');
        }

        /**
         * @var SponsorTicket $sponsorTicket
         */
        $sponsorTicket = $this->repositoryFactory->get(SponsorTicketRepository::class)->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            throw new InvalidSponsorTokenException('Token invalide.');
        }

        if (!$sponsorTicket->getQrCodesScannerAvailable()) {
            throw new InvalidSponsorTokenException('Accès non autorisé.');
        }

        return $sponsorTicket;
    }
}
