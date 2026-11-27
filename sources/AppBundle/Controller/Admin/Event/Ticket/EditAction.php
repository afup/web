<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Form\TicketAdminWithInvoiceType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Ticket\TicketOffers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
final class EditAction extends AbstractController
{
    public function __construct(
        private readonly TicketOffers $ticketOffers,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly EventRepository $eventRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $ticket = $this->inscriptionRepository->find($id);
        if (!$ticket instanceof Inscription) {
            throw $this->createNotFoundException(sprintf('Ticket not found with id "%s"', $id));
        }
        $event = $this->eventRepository->get($ticket->forumId);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Event not found with id "%s"', $ticket->forumId));
        }
        $invoice = $this->invoiceRepository->getByReference($ticket->reference);
        if (!$invoice instanceof Invoice) {
            throw $this->createNotFoundException(sprintf('Invoice not found with id "%s"', $ticket->reference));
        }

        $offers = $this->ticketOffers->getAllOffersForEvent($event);

        $form = $this->createForm(TicketAdminWithInvoiceType::class, [
            'ticket' => $ticket,
            'invoice' => $invoice,
        ], [
            'event' => $event,
            'offers' => $offers,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invoice->setStatus($ticket->status->value);

            $this->inscriptionRepository->save($ticket);
            $this->invoiceRepository->save($invoice);

            $this->audit->log(sprintf("Modification de l'inscription de %s (%d)", $ticket->getLabel(), $ticket->id));
            $this->addFlash('notice', "L'inscription a été modifiée.");

            return $this->redirectToRoute('admin_event_ticket_list');
        }

        return $this->render('admin/event/ticket/edit.html.twig', [
            'event' => $event,
            'ticket' => $ticket,
            'invoice' => $invoice,
            'form' => $form->createView(),
        ]);
    }
}
