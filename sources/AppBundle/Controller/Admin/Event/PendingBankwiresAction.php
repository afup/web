<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event;

use AppBundle\Email\Emails;
use AppBundle\Event\Invoice\EventInvoiceMailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Entity\Inscription;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;

#[IsGranted('ROLE_EVENT_WRITER')]
class PendingBankwiresAction extends AbstractController
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly Emails $emails,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly EventInvoiceMailer $invoiceMailer,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): Response
    {
        $id = $request->query->get('id');

        $event = $eventSelection->event;

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
            'pendingBankwires' => $this->getPendingBankwires($event),
            'event' => $event,
            'title' => 'Virements en attente',
            'token' => $this->csrfTokenManager->getToken('admin_event_bankwires'),
            'event_select_form' => $eventSelection->selectForm(),
        ]);
    }

    /**
     * @return list<array{invoice: Invoice, inscriptions: list<Inscription>}>
     */
    private function getPendingBankwires(Event $event): array
    {
        $pendingBankwires = [];
        foreach ($this->invoiceRepository->getPendingBankwires($event) as $invoice) {
            $pendingBankwires[] = [
                'invoice' => $invoice,
                'inscriptions' => $this->inscriptionRepository->getByReference((string) $invoice->getReference()),
            ];
        }

        return $pendingBankwires;
    }

    private function setInvoicePaid(Event $event, Invoice $invoice): void
    {
        $invoice
            ->setStatus(InscriptionEtat::Reglee->value)
            ->setPaymentDate(new DateTime());
        $this->invoiceRepository->save($invoice);
        $tickets = $this->inscriptionRepository->getByReference((string) $invoice->getReference());

        $this->invoiceMailer->send($invoice->getReference());
        $this->addFlash('notice', sprintf('La facture %s a été marquée comme payée', $invoice->getReference()));

        foreach ($tickets as $ticket) {
            $ticket->status = InscriptionEtat::Reglee;
            $ticket->invoiceStatus = InscriptionFacturation::Envoyee;
            $this->inscriptionRepository->save($ticket);

            $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use ($event, $ticket): int {
                $this->emails->sendInscription($event, new MailUser($ticket->email, $ticket->getLabel()));

                return 1;
            });
        }
    }
}
