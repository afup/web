<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Invoice\EventInvoiceReferenceGenerator;
use AppBundle\Event\Form\TicketAdminWithInvoiceType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Model\TicketOffer;
use AppBundle\Event\Ticket\TicketOffers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use AppBundle\Event\Enum\TypeReglement;

#[IsGranted('ROLE_EVENT_WRITER')]
final class AddAction extends AbstractController
{
    public function __construct(
        private readonly TicketOffers $ticketOffers,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly EventRepository $eventRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
        private readonly EventInvoiceReferenceGenerator $referenceGenerator,
    ) {}

    public function __invoke(Request $request): Response
    {
        $eventId = $request->query->getInt('eventId');
        $event = $this->eventRepository->get($eventId);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Event not found with id "%s"', $eventId));
        }

        $offers = $this->ticketOffers->getAllOffersForEvent($event);

        $ticket = new Inscription();
        $invoice = new Invoice();
        $invoice->setPaymentType(TypeReglement::Aucun->value);
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
            $ticketTypeId = $ticket->ticketTypeId;
            $offer = array_find($offers, fn(TicketOffer $offer): bool => $offer->ticketTypeId === $ticketTypeId);
            if (!$offer instanceof TicketOffer) {
                throw $this->createNotFoundException(sprintf('Offer not found with ticketTypeId "%s"', $ticketTypeId));
            }

            $reference = $this->referenceGenerator->generate($event->getId(), $ticket->getLabel());
            if ($offer->ticketEventType) {
                $ticket->setTicketEventType($offer->ticketEventType);
            }
            $ticket->forumId = (int) $event->getId();
            $ticket->amount = $offer->price;
            $ticket->date = new \DateTime();
            $ticket->reference = $reference;

            $invoice->setReference($reference);
            $invoice->setAmount($ticket->amount);
            $invoice->setForumId($ticket->forumId);
            $invoice->setStatus($ticket->invoiceStatus->value);
            $invoice->setStatus($ticket->status->value);
            $invoice->setInvoice(true);
            $invoice->setInvoiceDate($invoice->getPaymentDate());

            $this->inscriptionRepository->save($ticket);
            $this->invoiceRepository->save($invoice);

            $this->audit->log(sprintf("Ajout de l'inscription de %s (%d)", $ticket->getLabel(), $ticket->id));
            $this->addFlash('notice', "L'inscription a été ajoutée.");

            return $this->redirectToRoute('admin_event_ticket_list');
        }

        return $this->render('admin/event/ticket/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
