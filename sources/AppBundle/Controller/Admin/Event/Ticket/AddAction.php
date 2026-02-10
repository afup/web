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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AddAction extends AbstractController
{
    public function __construct(
        private readonly TicketOffers      $ticketOffers,
        private readonly TicketRepository  $ticketRepository,
        private readonly EventRepository   $eventRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit             $audit, private readonly Facturation $facturation,
    )
    {
    }

    public function __invoke(Request $request): Response
    {
        $eventId = $request->query->getInt('eventId');
        $event = $this->eventRepository->get($eventId);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Event not found with id "%s"', $eventId));
        }

        $offers = $this->ticketOffers->getAllOffersForEvent($event);

        $form = $this->createForm(TicketAdminWithInvoiceType::class, [], [
            'event' => $event,
            'offers' => $offers
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Ticket $ticket */
            /** @var Invoice $invoice */
            ['ticket' => $ticket, 'invoice' => $invoice] = $form->getData();

            $ticketTypeId = $ticket->getTicketTypeId();
            $offer = array_find($offers, function (TicketOffer $offer) use ($ticketTypeId): bool {
                return $offer->ticketTypeId === $ticketTypeId;
            });
            if (!$offer instanceof TicketOffer) {
                throw new NotFoundHttpException(sprintf('Offer not found with ticketTypeId "%s"', $ticketTypeId));
            }

            if ($offer->ticketEventType) {
                $ticket->setTicketEventType($offer->ticketEventType);
            }
            $ticket->setAmount($offer->price);
            $ticket->setDate(new \DateTime());
            $reference = $this->facturation->creerReference($event->getId(), $ticket->getLabel());
            $ticket->setReference($reference);
            $invoice->setReference($reference);

            $this->ticketRepository->save($ticket);
            $this->invoiceRepository->saveWithTickets($invoice);

            $this->audit->log(sprintf("Modification de l'inscription de %s (%d)", $ticket->getLabel(), $ticket->getId()));
            $this->addFlash('notice', "L'inscription a été modifiée.");

            return $this->redirectToRoute('admin_event_ticket_list');
        }

        return $this->render('admin/event/ticket/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
