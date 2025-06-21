<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Model\Repository\SponsorScanRepository;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class ExportAction extends SponsorScanController
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
}
