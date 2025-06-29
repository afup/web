<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Ticket;

use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Vat;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\PurchaseTypeFactory;
use AppBundle\LegacyModelFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TicketAction extends AbstractController
{
    public function __construct(
        private readonly PurchaseTypeFactory $purchaseTypeFactory,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly LegacyModelFactory $legacyModelFactory,
        private readonly TicketRepository $ticketRepository,
        private readonly EventActionHelper $eventActionHelper,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
    ) {}

    public function __invoke($eventSlug, Request $request): Response
    {
        $event = $this->eventActionHelper->getEvent($eventSlug);

        if ($event->getDateEndSales() < new \DateTime()) {
            return $this->render('event/ticket/sold_out.html.twig', ['event' => $event]);
        }

        $purchaseFactory = $this->purchaseTypeFactory;

        $purchaseForm = $purchaseFactory->getPurchaseForUser($event, $this->getUser(), $request->query->get('token', null));

        $purchaseForm->handleRequest($request);

        /**
         * @var User|null $user
         */
        $user = $this->getUser();

        if ($purchaseForm->isSubmitted() && $purchaseForm->isValid()) {
            $invoiceRepository = $this->invoiceRepository;
            /**
             * @var Invoice $invoice
             */
            $invoice = $purchaseForm->getData();

            /**
             * @var Ticket[] $tickets
             */
            $tickets = array_slice($invoice->getTickets(), 0, $purchaseForm->get('nbPersonnes')->getData());
            $tickets[0]
                ->setCompanyCitation($purchaseForm->get('companyCitation')->getData())
                ->setNewsletter($purchaseForm->get('newsletterAfup')->getData())
            ;

            if ($user instanceof User) {
                $memberId = $user->getId();
                $memberType = UserRepository::USER_TYPE_PHYSICAL;
                if ($user->isMemberForCompany()) {
                    $memberId = $user->getCompanyId();
                    $memberType = UserRepository::USER_TYPE_COMPANY;
                }
            }

            foreach ($tickets as $ticket) {
                if ($ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers() && isset($memberId, $memberType)) {
                    $ticket
                        ->setMemberId($memberId)
                        ->setMemberType($memberType)
                    ;
                }
            }

            $invoice->setTickets($tickets);

            /**
             * @todo: voir où le mettre ça
             */
            $reference = $this->legacyModelFactory->createObject(Facturation::class)->creerReference($event->getId(), $invoice->getLabel());
            $invoice->setReference($reference);
            $invoiceRepository->saveWithTickets($invoice);

            return $this->redirectToRoute('ticket_payment', ['eventSlug' => $eventSlug, 'invoiceRef' => $invoice->getReference()]);
        }

        $totalOfSoldTicketsByMember = 0;
        if ($user !== null) {
            $totalOfSoldTicketsByMember = $this->ticketRepository->getTotalOfSoldTicketsByMember(
                $user->isMemberForCompany() ? UserRepository::USER_TYPE_COMPANY : UserRepository::USER_TYPE_PHYSICAL,
                $user->isMemberForCompany() ? $user->getCompanyId() : $user->getId(),
                $event->getId(),
            );
        }

        return $this->render('event/ticket/ticket.html.twig', [
            'event' => $event,
            'ticketForm' => $purchaseForm->createView(),
            'nbPersonnes' => $purchaseForm->get('nbPersonnes')->getData(), // If there is an error, this will open all fields
            'maxNbPersonnes' => count($purchaseForm->getData()->getTickets()),
            'isSubjectedToVat' => Vat::isSubjectedToVat(new \DateTime('now')),
            'hasPricesDefinedWithVat' => $event->hasPricesDefinedWithVat(),
            'soldTicketsForMember' => $totalOfSoldTicketsByMember,
            'hasMembersTickets' => $this->ticketEventTypeRepository->doesEventHasRestrictedToMembersTickets($event, true, TicketEventTypeRepository::REMOVE_PAST_TICKETS),
        ]);
    }
}
