<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\SponsorScan;

use AppBundle\Controller\Exception\InvalidSponsorTokenException;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Entity\SponsorTicket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class SponsorScanController extends AbstractController
{
    public function __construct(private readonly SponsorTicketRepository $sponsorTicketRepository) {}

    protected function checkSponsorTicket(Request $request): SponsorTicket
    {
        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            throw new InvalidSponsorTokenException('Merci de renseigner votre token.');
        }

        $sponsorTicket = $this->sponsorTicketRepository->find($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            throw new InvalidSponsorTokenException('Token invalide.');
        }

        if (!$sponsorTicket->qrCodesScannerAvailable) {
            throw new InvalidSponsorTokenException('Accès non autorisé.');
        }

        return $sponsorTicket;
    }
}
