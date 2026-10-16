<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\SponsorTicketRepository;
use AppBundle\Event\Entity\SponsorTicket;
use AppBundle\Event\Form\SponsorTokenType;
use AppBundle\Event\Ticket\SponsorTokenMail;
use AppBundle\Security\Authentication;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SponsorTicketAction extends AbstractController
{
    public function __construct(
        private readonly SponsorTicketRepository $sponsorTicketRepository,
        private readonly SponsorTokenMail $sponsorTokenMail,
        private readonly Authentication $authentication,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $event = $eventSelection->event;
        $tokens = $this->sponsorTicketRepository->findByEventId((int) $event->getId());
        $edit = $request->query->has('ticket');
        $now = new DateTimeImmutable();
        if ($edit) {
            $newToken = $this->sponsorTicketRepository->find($request->query->get('ticket'));
            if ($newToken === null) {
                throw $this->createNotFoundException('Could not find token');
            }
            $newToken->editedOn = $now;
        } else {
            $newToken = new SponsorTicket();
            $newToken->token = base64_encode(random_bytes(30));
            $newToken->eventId = (int) $event->getId();
            $newToken->createdOn = $now;
            $newToken->editedOn = $now;
            $newToken->creatorId = $this->authentication->getAfupUser()->getId();
        }
        $form = $this->createForm(SponsorTokenType::class, $newToken);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($newToken->id === null) {
                $this->sponsorTokenMail->sendNotification($newToken);
            }
            $this->sponsorTicketRepository->save($newToken);
            $this->addFlash('notice', 'Le token a été enregistré');

            return $this->redirectToRoute('admin_event_sponsor_ticket', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('admin/event/sponsor_ticket.html.twig', [
            'tokens' => $tokens,
            'event' => $event,
            'title' => 'Gestion des inscriptions sponsors',
            'form' => $form->createView(),
            'edit' => $edit,
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }
}
