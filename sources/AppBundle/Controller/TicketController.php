<?php

namespace AppBundle\Controller;
use Afup\Site\Forum\Facturation;
use AppBundle\Event\Form\SponsorTicketType;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\SponsorTicketRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\SponsorTicket;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\PurchaseTypeFactory;
use CCMBenchmark\Ting\Driver\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;

class TicketController extends EventBaseController
{
    public function sponsorTicketAction(Request $request, $eventSlug)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($event->getDateEndSales() < new \DateTime()) {
            return $this->render(':event/ticket:sold_out.html.twig', ['event' => $event]);
        }
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
                 * @var $sponsorTicket SponsorTicket
                 */
                $sponsorTicket = $this->get('ting')->get(SponsorTicketRepository::class)->getOneBy(['token' => $token]);
                if (
                    $this->get('app.action_throttling')->isActionBlocked('sponsor_token', $request->getClientIp(), null)
                    ||
                    $sponsorTicket === null
                ) {
                    // Si l'IP a fait trop de tentatives, on affiche le meme message que si le token n'existe pas
                    // L'ip est bloquée pendant un temps mais il ne faut pas en informer celui qui tente - pour éviter
                    // qu'il ne change d'IP
                    $errors[] = 'Ce token n\'existe pas.';
                    $this->get('app.action_throttling')->log('sponsor_token', $request->getClientIp(), null);
                } else {
                    $request->getSession()->set('sponsor_ticket_id', $sponsorTicket->getId());
                    $this->get('app.action_throttling')->clearLogsForIp('sponsor_token', $request->getClientIp());

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

        if ($event->getDateEndSales() < new \DateTime()) {
            return $this->render(':event/ticket:sold_out.html.twig', ['event' => $event]);
        }

        if ($request->getSession()->has('sponsor_ticket_id') === false) {
            $this->addFlash('error', 'Merci de renseigner votre token');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        /**
         * @var $sponsorTicket SponsorTicket
         */
        $sponsorTicket = $this->get('ting')->get(SponsorTicketRepository::class)->get($request->getSession()->get('sponsor_ticket_id'));
        if ($sponsorTicket === null) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('sponsor_ticket_home', ['eventSlug' => $eventSlug]);
        }

        $ticketFactory = $this->get('app.ticket_factory');

        $sponsorTicketHelper = $this->get('app.sponsor_ticket_helper');
        $edit = false;
        if ($request->query->has('ticket')) {
            /**
             * @var $ticket Ticket
             */
            $ticket = $this->get('ting')->get(TicketRepository::class)->get($request->query->get('ticket'));

            if ($ticket === null || $sponsorTicketHelper->doesTicketBelongsToSponsor($sponsorTicket, $ticket) === false) {
                throw $this->createNotFoundException();
            }
            $edit = true;
        } else {
            $ticket = $ticketFactory->createTicketFromSponsorTicket($sponsorTicket);
        }
        $ticketForm = $this->createForm(SponsorTicketType::class, $ticket);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted() && $ticketForm->isValid() && $sponsorTicket->getPendingInvitations() > 0) {
            $sponsorTicketHelper->addTicketToSponsor($sponsorTicket, $ticket);
            $mailer = $this->get('app.mail');
            $logger = $this->get('logger');
            $this->get('event_dispatcher')->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket, $mailer, $logger) {
                $receiver = [
                    'email' => $ticket->getEmail(),
                    'name'  => $ticket->getLabel(),
                ];

                if (!$mailer->send($event->getMailTemplate(), $receiver, [])) {
                    $logger->addWarning(sprintf('Mail not sent for inscription %s', $ticket->getEmail()));
                }
                return 1;
            });

            $this->addFlash('notice', 'Invitation enregistrée');
            return $this->redirectToRoute('sponsor_ticket_form', ['eventSlug' => $eventSlug]);
        } elseif ($request->isMethod(Request::METHOD_POST) && $request->request->has('delete')) {
            /**
             * @var $ticket Ticket
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
            'sponsorTicket' => $sponsorTicket,
            'ticketForm' => $ticketForm->createView(),
            'registeredTickets' => $sponsorTicketHelper->getRegisteredTickets($sponsorTicket),
            'edit' => $edit
        ]);
    }

    public function ticketAction($eventSlug, Request $request)
    {
        $event = $this->checkEventSlug($eventSlug);

        if ($event->getDateEndSales() < new \DateTime()) {
            return $this->render(':event/ticket:sold_out.html.twig', ['event' => $event]);
        }

        /* @todo service */
        $purchaseFactory = new PurchaseTypeFactory(
            $this->get('security.authorization_checker'),
            $this->container->get('form.factory'),
            $this->container->get('app.invoice_factory')
        );

        $purchaseForm = $purchaseFactory->getPurchaseForUser($event, $request->getUser());

        $purchaseForm->handleRequest($request);

        if ($purchaseForm->isSubmitted() && $purchaseForm->isValid()) {

            $invoiceRepository = $this->get('app.invoice_repository');
            /**
             * @var $invoice Invoice
             */
            $invoice = $purchaseForm->getData();

            /**
             * @todo: voir où le mettre ça
             */
            $reference = $this->get('app.legacy_model_factory')->createObject(Facturation::class)->creerReference($event->getId(), $invoice->getLabel());
            $invoice->setReference($reference);
            $invoiceRepository->saveWithTickets($invoice);

        }

        return $this->render('event/ticket/ticket.html.twig', [
            'event' => $event,
            'ticketForm' => $purchaseForm->createView()
        ]);
    }
}
