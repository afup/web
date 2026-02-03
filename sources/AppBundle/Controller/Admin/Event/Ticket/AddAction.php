<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use Afup\Site\Forum\Facturation;
use AppBundle\AuditLog\Audit;
use AppBundle\Event\Form\TicketAdminWithInvoiceType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketOffer;
use AppBundle\Event\Ticket\TicketOffers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddAction extends AbstractController
{
    public function __construct(
        private readonly TicketOffers $ticketOffers,
        private readonly TicketRepository $ticketRepository,
        private readonly EventRepository $eventRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
        private readonly Facturation $facturation,
    ) {}

    public function __invoke(Request $request): Response
    {
        $eventId = $request->query->getInt('eventId');
        $event = $this->eventRepository->get($eventId);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Event not found with id "%s"', $eventId));
        }

        $offers = $this->ticketOffers->getAllOffersForEvent($event);

        $ticket = new Ticket();
        $invoice = new Invoice();
        $invoice->setPaymentType(Ticket::PAYMENT_NONE);
        $invoice->setPaymentDate(new \DateTime());

        $form = $this->createForm(TicketAdminWithInvoiceType::class, [
            'ticket' => $ticket,
            'invoice' => $invoice,
        ], [
            'event' => $event,
            'offers' => $offers,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            ['ticket' => $ticket, 'invoice' => $invoice] = $form->getData();

            $ticketTypeId = $ticket->getTicketTypeId();
            $offer = array_find($offers, fn(TicketOffer $offer): bool => $offer->ticketTypeId === $ticketTypeId);
            if (!$offer instanceof TicketOffer) {
                throw $this->createNotFoundException(sprintf('Offer not found with ticketTypeId "%s"', $ticketTypeId));
            }

            $reference = $this->facturation->creerReference($event->getId(), $ticket->getLabel());
            if ($offer->ticketEventType) {
                $ticket->setTicketEventType($offer->ticketEventType);
            }
            $ticket->setForumId($event->getId());
            $ticket->setAmount($offer->price);
            $ticket->setDate(new \DateTime());
            $ticket->setReference($reference);

            $invoice->setReference($reference);
            $invoice->setAmount($ticket->getAmount());
            $invoice->setForumId($ticket->getForumId());
            $invoice->setStatus($ticket->getInvoiceStatus());
            $invoice->setStatus($ticket->getStatus());
            $invoice->setInvoice(true);
            $invoice->setInvoiceDate($invoice->getPaymentDate());

            $this->ticketRepository->save($ticket);
            $this->invoiceRepository->save($invoice);

            $this->audit->log(sprintf("Ajout de l'inscription de %s (%d)", $ticket->getLabel(), $ticket->getId()));
            $this->addFlash('notice', "L'inscription a été ajoutée.");

            return $this->redirectToRoute('admin_event_ticket_list');
        }

        return $this->render('admin/event/ticket/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
