<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Event\Ticket;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteAction extends AbstractController
{
    public function __construct(
        private readonly TicketRepository $ticketRepository,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(int $id, Request $request): Response
    {
        $ticket = $this->ticketRepository->get($id);
        if (!$ticket instanceof Ticket) {
            throw $this->createNotFoundException(sprintf('Ticket not found with id "%s"', $id));
        }

        $invoice = $this->invoiceRepository->getByReference($id);
        if ($invoice) {
            $this->invoiceRepository->delete($invoice);
        }
        $this->ticketRepository->delete($ticket);

        $this->audit->log("Suppression de l'inscription " . $id);
        $this->addFlash('notice', "L'inscription a été supprimée");

        return $this->redirectToRoute('admin_event_ticket_list');
    }
}
