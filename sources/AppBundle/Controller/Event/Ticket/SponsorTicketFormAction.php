<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Form\SponsorTicketType;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\TicketFactory;
use AppBundle\Event\Ticket\SponsorTicketHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

final class SponsorTicketFormAction extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TicketFactory $ticketFactory,
        private readonly SponsorTicketHelper $sponsorTicketHelper,
        private readonly Emails $emails,
        private readonly TicketRepository $ticketRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly SponsorTicketRepository $sponsorTicketRepository,
    ) {}

    public function __invoke(Request $request, $eventSlug): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            $this->addFlash('error', 'Merci de renseigner votre token');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $sponsorTicket = $this->sponsorTicketRepository->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $ticketFactory = $this->ticketFactory;

        $sponsorTicketHelper = $this->sponsorTicketHelper;
        $edit = false;
        if ($request->query->has('ticket')) {
            $ticket = $this->ticketRepository->get($request->query->get('ticket'));

            if ($ticket === null || $sponsorTicketHelper->doesTicketBelongsToSponsor($sponsorTicket, $ticket) === false) {
                throw $this->createNotFoundException();
            }
            $edit = true;
        } else {
            $ticket = $ticketFactory->createTicketFromSponsorTicket($sponsorTicket);
        }
        $ticketForm = $this->createForm(SponsorTicketType::class, $ticket, ['with_transport' => $event->getTransportInformationEnabled()]);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted() && $ticketForm->isValid()) {
            // Si c'est l'ajout d'un ticket
            // Et qu'il n'y a plus d'invitation
            // ou que la date du sponsoring est pas passée
            if ($ticket->getId() === null && ($sponsorTicket->getPendingInvitations() <= 0 || $event->getDateEndSalesSponsorToken() < new \DateTime())) {
                return $this->render('event/ticket/sold_out.html.twig', ['event' => $event]);
            }

            $sponsorTicketHelper->addTicketToSponsor($sponsorTicket, $ticket);
            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                $this->emails->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                return 1;
            });

            $this->addFlash('notice', 'Invitation enregistrée');
            return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
        } elseif ($request->isMethod(Request::METHOD_POST) && $request->request->has('delete')) {
            $ticket = $this->ticketRepository->get($request->request->get('delete'));

            if ($ticket === null) {
                $this->addFlash('error', 'Impossible de trouver ce ticket');

                return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
            }
            try {
                $sponsorTicketHelper->removeTicketFromSponsor($sponsorTicket, $ticket);
                $this->addFlash('notice', 'Le billet a été supprimé');
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }

            return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
        }

        return $this->render('event/ticket/sponsor.html.twig', [
            'event' => $event,
            'sponsors_infos' => $event->getSponsorInfos($request->getLocale()),
            'sponsorTicket' => $sponsorTicket,
            'with_transport' => $event->getTransportInformationEnabled(),
            'ticketForm' => $ticketForm->createView(),
            'registeredTickets' => $sponsorTicketHelper->getRegisteredTickets($sponsorTicket),
            'edit' => $edit,
            'sold_out' => $event->getDateEndSalesSponsorToken() < new \DateTime(),
        ]);
    }
}
