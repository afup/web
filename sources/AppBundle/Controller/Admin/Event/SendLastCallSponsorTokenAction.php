<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Ticket\SponsorTokenMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SendLastCallSponsorTokenAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
        private readonly SponsorTokenMail $sponsorTokenMail,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getFromRequest('id', false)->event;
        /** @var SponsorTicket[] $tokens */
        $tokens = $this->sponsorTicketRepository->findByEventId((int) $event->getId());
        $mailSent = 0;

        foreach ($tokens as $token) {
            if ($token->getPendingInvitations() > 0) {
                $mailSent++;
                $this->sponsorTokenMail->sendNotification($token, true);
            }
        }

        $this->addFlash('notice', sprintf('%s mails de relance ont été envoyés', $mailSent));

        return $this->redirectToRoute('admin_event_sponsor_ticket', [
            'id' => $event->getId(),
        ]);
    }
}
