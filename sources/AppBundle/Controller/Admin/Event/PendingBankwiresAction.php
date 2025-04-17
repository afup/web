<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use Afup\Site\Forum\Facturation;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Email\Emails;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\LegacyModelFactory;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PendingBankwiresAction extends AbstractController
{
    private EventActionHelper $eventActionHelper;
    private InvoiceRepository $invoiceRepository;
    private TicketRepository $ticketRepository;
    private LegacyModelFactory $legacyModelFactory;
    private Emails $emails;
    private EventDispatcherInterface $eventDispatcher;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        EventActionHelper $eventActionHelper,
        InvoiceRepository $invoiceRepository,
        TicketRepository $ticketRepository,
        LegacyModelFactory $legacyModelFactory,
        Emails $emails,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->eventActionHelper = $eventActionHelper;
        $this->invoiceRepository = $invoiceRepository;
        $this->ticketRepository = $ticketRepository;
        $this->legacyModelFactory = $legacyModelFactory;
        $this->emails = $emails;
        $this->eventDispatcher = $eventDispatcher;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->query->get('id');

        $event = $this->eventActionHelper->getEventById($id);

        if ($request->isMethod(Request::METHOD_POST)) {
            if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('admin_event_bankwires',
                $request->request->get('token')))) {
                $this->addFlash('error', 'Erreur de token CSRF, veuillez réessayer');
            } else {
                $reference = $request->request->get('bankwireReceived');
                $invoice = $this->invoiceRepository->getByReference($reference);
                if ($invoice === null) {
                    throw $this->createNotFoundException(sprintf('No invoice with this reference: "%s"', $reference));
                }
                $this->setInvoicePaid($event, $invoice);
            }
        }

        return $this->render('admin/event/bankwires.html.twig', [
            'pendingBankwires' => $event === null ? [] : $this->invoiceRepository->getPendingBankwires($event),
            'event' => $event,
            'title' => 'Virements en attente',
            'token' => $this->csrfTokenManager->getToken('admin_event_bankwires'),
            'event_select_form' => $this->createForm(EventSelectType::class, $event)->createView(),
        ]);
    }

    private function setInvoicePaid(Event $event, Invoice $invoice): void
    {
        $invoice
            ->setStatus(Ticket::STATUS_PAID)
            ->setPaymentDate(new DateTime());
        $this->invoiceRepository->save($invoice);
        $tickets = $this->ticketRepository->getByReference($invoice->getReference());

        $forumFacturation = $this->legacyModelFactory->createObject(Facturation::class);
        $forumFacturation->envoyerFacture($invoice->getReference());
        $this->addFlash('notice', sprintf('La facture %s a été marquée comme payée', $invoice->getReference()));

        foreach ($tickets as $ticket) {
            $ticket
                ->setStatus(Ticket::STATUS_PAID)
                ->setInvoiceStatus(Ticket::INVOICE_SENT);
            $this->ticketRepository->save($ticket);

            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                $this->emails->sendInscription($event, new MailUser($ticket->getEmail(), $ticket->getLabel()));

                return 1;
            });
        }
    }
}
