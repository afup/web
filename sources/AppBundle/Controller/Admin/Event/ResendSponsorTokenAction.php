<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Ticket\SponsorTokenMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ResendSponsorTokenAction extends AbstractController
{
    public function __construct(
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
        private readonly SponsorTokenMail $sponsorTokenMail,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'));
        $token = $this->sponsorTicketRepository->get($request->request->get('sponsor_token_id'));
        if ($token === null) {
            throw $this->createNotFoundException(sprintf('Could not find token with id: %s', $request->request->get('sponsor_token_id')));
        }
        $this->sponsorTokenMail->sendNotification($token);

        $this->addFlash('notice', 'Le mail a été renvoyé');

        return $this->redirectToRoute('admin_event_sponsor_ticket', [
            'id' => $event->getId(),
        ]);
    }
}
