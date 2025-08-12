<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Ticket\SponsorTicketHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteSponsorTicketAction extends AbstractController implements AdminActionWithEventSelector
{
    public function __construct(
        private readonly SponsorTicketRepository $sponsorTicketRepository,
        private readonly SponsorTicketHelper $sponsorTicketHelper,
    ) {}

    public function __invoke(Request $request, int $tokenId): Response
    {
        $token = $this->sponsorTicketRepository->get($tokenId);
        if ($token === null) {
            throw $this->createNotFoundException(sprintf('Could not find token with id: %s', $tokenId));
        }

        if ($this->sponsorTicketHelper->getRegisteredTickets($token)->count() > 0) {
            $this->addFlash('error', 'Le token ne peut être supprimé.');
            return $this->redirectToRoute('admin_event_sponsor_ticket');
        }
        $this->sponsorTicketRepository->delete($token);

        $this->addFlash('notice', 'Le token a été supprimé');
        return $this->redirectToRoute('admin_event_sponsor_ticket');
    }
}
