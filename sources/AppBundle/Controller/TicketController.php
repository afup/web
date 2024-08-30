<?php

namespace AppBundle\Controller;

use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Utils;
use Afup\Site\Utils\Vat;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Compta\BankAccount\BankAccountFactory;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Form\SponsorTicketType;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use AppBundle\Payment\PayboxResponse;
use AppBundle\Payment\PayboxResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class TicketController extends EventBaseController
{
    public function sponsorTicketAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($request->getSession()->has('sponsor_ticket_id') === true) {
            $request->getSession()->remove('sponsor_ticket_id');
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $csrf = $this->get('security.csrf.token_manager')->getToken('sponsor_ticket');
            $errors = [];
            if ($csrf->getValue() !== $request->get('_csrf_token')) {
                $errors[] = 'Jeton anti csrf invalide';
            } elseif ($request->request->has('sponsor_token') === false) {
                $errors[] = 'Token absent';
            } else {
                $token = $request->request->get('sponsor_token');
                /**
                 * @var SponsorTicket $sponsorTicket
                 */
                $sponsorTicket = $this->get('ting')->get(SponsorTicketRepository::class)->getOneBy(['token' => $token]);
                if (
                    $this->get(\AppBundle\Security\ActionThrottling\ActionThrottling::class)->isActionBlocked('sponsor_token', $request->getClientIp(), null)
                    ||
                    $sponsorTicket === null
                ) {
                    // Si l'IP a fait trop de tentatives, on affiche le meme message que si le token n'existe pas
                    // L'ip est bloquée pendant un temps mais il ne faut pas en informer celui qui tente - pour éviter
                    // qu'il ne change d'IP
                    $errors[] = 'Ce token n\'existe pas.';
                    $this->get(\AppBundle\Security\ActionThrottling\ActionThrottling::class)->log('sponsor_token', $request->getClientIp(), null);
                } else {
                    $request->getSession()->set('sponsor_ticket_id', $sponsorTicket->getId());
                    $this->get(\AppBundle\Security\ActionThrottling\ActionThrottling::class)->clearLogsForIp('sponsor_token', $request->getClientIp());

                    return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
                }
            }
            if ($errors !== []) {
                $this->get('session')->getFlashBag()->setAll(['error' => $errors]);
                return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
            }
        }

        return $this->render(':event/ticket:sponsor_home.html.twig', ['event' => $event]);
    }

    public function sponsorTicketFormAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            $this->addFlash('error', 'Merci de renseigner votre token');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        /**
         * @var SponsorTicket $sponsorTicket
         */
        $sponsorTicket = $this->get('ting')->get(SponsorTicketRepository::class)->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $ticketFactory = $this->get(\AppBundle\Event\Model\TicketFactory::class);

        $sponsorTicketHelper = $this->get(\AppBundle\Event\Ticket\SponsorTicketHelper::class);
        $edit = false;
        if ($request->query->has('ticket')) {
            /**
             * @var Ticket $ticket
             */
            $ticket = $this->get('ting')->get(TicketRepository::class)->get($request->query->get('ticket'));

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
                return $this->render(':event/ticket:sold_out.html.twig', ['event' => $event]);
            }

            $sponsorTicketHelper->addTicketToSponsor($sponsorTicket, $ticket);
            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket) {
                $this->get(Emails::class)->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                return 1;
            });

            $this->addFlash('notice', 'Invitation enregistrée');
            return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
        } elseif ($request->isMethod(Request::METHOD_POST) && $request->request->has('delete')) {
            /**
             * @var Ticket $ticket
             */
            $ticket = $this->get('ting')->get(TicketRepository::class)->get($request->request->get('delete'));

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

    public function ticketAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($event->getDateEndSales() < new \DateTime()) {
            return $this->render(':event/ticket:sold_out.html.twig', ['event' => $event]);
        }

        $purchaseFactory = $this->get(\AppBundle\Event\Ticket\PurchaseTypeFactory::class);

        $purchaseForm = $purchaseFactory->getPurchaseForUser($event, $this->getUser(), $request->query->get('token', null));

        $purchaseForm->handleRequest($request);

        /**
         * @var User|null $user
         */
        $user = $this->getUser();

        if ($purchaseForm->isSubmitted() && $purchaseForm->isValid()) {
            $invoiceRepository = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class);
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
                if ($ticket->getTicketEventType()->getTicketType()->getIsRestrictedToMembers()) {
                    if (isset($memberId, $memberType)) {
                        $ticket
                            ->setMemberId($memberId)
                            ->setMemberType($memberType)
                        ;
                    }
                }
            }

            $invoice->setTickets($tickets);

            /**
             * @todo: voir où le mettre ça
             */
            $reference = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Facturation::class)->creerReference($event->getId(), $invoice->getLabel());
            $invoice->setReference($reference);
            $invoiceRepository->saveWithTickets($invoice);

            return $this->redirectToRoute('ticket_payment', ['eventSlug' => $eventSlug, 'invoiceRef' => $invoice->getReference()]);
        }

        $totalOfSoldTicketsByMember = 0;
        if ($user !== null) {
            $totalOfSoldTicketsByMember = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getTotalOfSoldTicketsByMember(
                $user->isMemberForCompany() ? UserRepository::USER_TYPE_COMPANY : UserRepository::USER_TYPE_PHYSICAL,
                $user->isMemberForCompany() ? $user->getCompanyId() : $user->getId(),
                $event->getId()
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
            'hasMembersTickets' => $this->get('ting')->get(TicketEventTypeRepository::class)->doesEventHasRestrictedToMembersTickets($event, true, TicketEventTypeRepository::REMOVE_PAST_TICKETS),

        ]);
    }

    public function paymentAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        $invoiceRepository = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class);

        $invoiceRef = $request->get('invoiceRef', $request->query->get('invoiceRef', null));
        /** @var Invoice $invoice */
        $invoice = $invoiceRepository->getByReference($invoiceRef);

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('Could not find invoice with reference "%s"', $invoiceRef));
        }

        if ($invoice->getStatus() === Ticket::STATUS_PAID) {
            $this->get('logger')->addWarning(
                sprintf('Invoice %s already paid, cannot show the paymentAction', $invoiceRef)
            );
            return $this->render(':event/ticket:payment_already_done.html.twig', ['event' => $event]);
        }

        $amount = $invoice->getAmount();
        if (false === $event->hasPricesDefinedWithVat()) {
            $amount = Vat::getRoundedWithVatPriceFromPriceWithoutVat($amount, Utils::TICKETING_VAT_RATE);
        }


        $params = [
            'event' => $event,
            'invoice' => $invoice,
            'amount' => $amount,
            'tickets' => $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getByInvoiceWithDetail(
                $invoice
            )
        ];

        if ($invoice->isFree()) {
            $invoice->setStatus(Ticket::STATUS_PAID);
            $invoice->setInvoiceDate(new \DateTime('now'));
            $invoice->setPaymentDate(new \DateTime('now'));
            $invoice->setInvoice(true);
            $invoice->setPaymentType(Ticket::PAYMENT_NONE);

            $invoiceRepository->save($invoice);

            $forumFacturation = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Facturation::class);
            $forumFacturation->envoyerFacture($invoice->getReference());

            $ticketRepository = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class);
            $tickets = $ticketRepository->getByInvoiceWithDetail($invoice);

            /** @var Ticket $ticket */
            foreach ($tickets as $ticket) {
                $ticket->setStatus(Ticket::STATUS_PAID);
                $ticket->setInvoiceStatus(Ticket::INVOICE_SENT);
                $ticketRepository->save($ticket);

                $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket) {
                    $this->get(Emails::class)->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                    return 1;
                });
            }
        } elseif ($invoice->getPaymentType() === Ticket::PAYMENT_CREDIT_CARD) {
            $params['paybox'] = $this->get(\AppBundle\Payment\PayboxFactory::class)->createPayboxForTicket($invoice, $event, $amount);
        } elseif ($invoice->getPaymentType() === Ticket::PAYMENT_BANKWIRE) {
            $bankAccountFactory = new BankAccountFactory();
            $params['bankAccount'] = $bankAccountFactory->createApplyableAt($invoice->getinvoiceDate());

            // For bankwire, companies need to retrieve the invoice
            $forumFacturation = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Facturation::class);
            $forumFacturation->envoyerFacture($invoiceRef);
        }

        return $this->render('event/ticket/payment.html.twig', $params);
    }

    /**
     * Action vers laquelle paybox post les résultats du paiement en serveur à serveur
     *
     * @param $eventSlug
     * @param Request $request
     * @return Response
     */
    public function payboxCallbackAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        $invoice = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class)->getByReference($request->get('cmd'));

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $request->get('cmd')));
        }

        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        $paymentStatus = Ticket::STATUS_ERROR;
        $invoiceStatus = Ticket::INVOICE_TODO;
        if ($payboxResponse->isSuccessful()) {
            $paymentStatus = Ticket::STATUS_PAID;
            $invoiceStatus = Ticket::INVOICE_SENT;
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_DUPLICATE) {
            // Designe un paiement deja effectue : on a surement deja eu le retour donc on s'arrete
            return new Response();
        } elseif ($payboxResponse->getStatus() === PayboxResponse::STATUS_CANCELED) {
            $paymentStatus = Ticket::STATUS_CANCELLED;
        } elseif ($payboxResponse->isErrorCode()) {
            $paymentStatus = Ticket::STATUS_DECLINED;
        }
        $invoice
            ->setStatus($paymentStatus)
            ->setPaymentDate(new \DateTime())
            ->setAuthorization($payboxResponse->getAuthorizationId())
            ->setTransaction($payboxResponse->getTransactionId())
        ;
        $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class)->save($invoice);
        $tickets = $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->getByReference($invoice->getReference());

        if ($paymentStatus === Ticket::STATUS_PAID) {
            /**
             * @var Facturation $forumFacturation
             */
            $forumFacturation = $this->get(\AppBundle\LegacyModelFactory::class)->createObject(Facturation::class);
            $forumFacturation->envoyerFacture($invoice->getReference());
        }

        foreach ($tickets as $ticket) {
            /**
             * @var $ticket Ticket
             */
            $ticket
                ->setStatus($paymentStatus)
                ->setInvoiceStatus($invoiceStatus)
            ;
            $this->get(\AppBundle\Event\Model\Repository\TicketRepository::class)->save($ticket);

            if ($paymentStatus === Ticket::STATUS_PAID) {
                $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket) {
                    $this->get(Emails::class)->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));
                    return 1;
                });
            }
        }
        return new Response();
    }

    /**
     * Action vers laquelle l'utilisateur est redirigé après paiement (ou tentative)
     *
     * @param $eventSlug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function payboxRedirectAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);
        $invoice = $this->get(\AppBundle\Event\Model\Repository\InvoiceRepository::class)->getByReference($request->get('cmd'));

        if ($invoice === null) {
            throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $request->get('cmd')));
        }

        $payboxResponse = PayboxResponseFactory::createFromRequest($request);

        return $this->render(':event/ticket:paybox_redirect.html.twig', [
            'event' => $event,
            'invoice' => $invoice,
            'payboxResponse' => $payboxResponse,
            'status' => $request->get('status')
        ]);
    }
}
