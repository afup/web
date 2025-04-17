<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Ticket\SponsorTokenMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SendLastCallSponsorTokenAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private SponsorTicketRepository $sponsorTicketRepository;
    private SponsorTokenMail $sponsorTokenMail;

    public function __construct(
        EventActionHelper $eventActionHelper,
        SponsorTicketRepository $sponsorTicketRepository,
        SponsorTokenMail $sponsorTokenMail
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->sponsorTicketRepository = $sponsorTicketRepository;
        $this->sponsorTokenMail = $sponsorTokenMail;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'), false);
        /** @var SponsorTicket[] $tokens */
        $tokens = $this->sponsorTicketRepository->getByEvent($event);
        $mailSent = 0;

        foreach ($tokens as $token) {
            if ($token->getPendingInvitations() > 0) {
                $mailSent++;
                $this->sponsorTokenMail->sendNotification($token, true);
            }
        }

        $this->addFlash('notice', sprintf('%s mails de relance ont été envoyés', $mailSent));

        return $this->redirectToRoute('admin_event_sponsor_ticket', [
            'id' => $event->getId()
        ]);
    }
}
