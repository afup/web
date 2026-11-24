<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Entity\Repository\PlanningRepository;
use AppBundle\Event\Invoice\InvoiceService;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Ticket;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerRegisterAction extends AbstractController
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly PlanningRepository $planningRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly InvoiceService $invoiceService,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): RedirectResponse
    {
        $event = $eventSelection->event;
        $talkAggregates = $this->planningRepository->enrichTalkAggregates(
            $this->talkRepository->getByEventWithSpeakers($event),
        );
        $nbSpeakers = 0;

        foreach ($talkAggregates as $talkAggregate) {
            if ($talkAggregate->talk->getType() === Talk::TYPE_PHP_PROJECT) {
                continue;
            }

            foreach ($talkAggregate->speakers as $speaker) {
                $reference = 'GENCONF-' . $event->getId() . '-' . $speaker->id . '-' . Ticket::TYPE_SPEAKER;
                $invoice = $this->invoiceRepository->getByReference($reference);

                if ($invoice) {
                    continue;
                }

                $ticket = new Ticket();
                $ticket->setDate(new DateTime());
                $ticket->setAmount(0);
                $ticket->setForumId($event->getId());
                $ticket->setReference($reference);
                $ticket->setTicketTypeId(Ticket::TYPE_SPEAKER);
                $ticket->setGenre($speaker->genre);
                $ticket->setLastname($speaker->lastname ?? '');
                $ticket->setFirstname($speaker->firstname ?? '');
                $ticket->setEmail($speaker->email ?? '');
                $ticket->setCompanyCitation(true);
                $ticket->setNewsletter(true);
                $ticket->setComments('import auto');
                $ticket->setStatus(Ticket::STATUS_GUEST);
                $ticket->setInvoiceStatus(Ticket::INVOICE_TODO);

                try {
                    $this->ticketRepository->save($ticket);
                } catch (Exception) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'inscription');

                    return $this->redirectToRoute('admin_speaker_list');
                }

                try {
                    $this->invoiceService->handleInvoicing(
                        $reference,
                        Ticket::PAYMENT_NONE,
                        '',
                        null,
                        $speaker->email,
                        $speaker->company,
                        $speaker->lastname,
                        $speaker->firstname,
                        '',
                        '',
                        '',
                        'FR',
                        $event->getId(),
                        null,
                        '',
                        '',
                        Ticket::STATUS_GUEST,
                    );
                    $this->audit->log('Ajout inscription conférencier ' . $speaker->id);
                    $nbSpeakers++;
                } catch (Exception) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de la facturation');

                    return $this->redirectToRoute('admin_speaker_list', [
                        'eventId' => $event->getId(),
                    ]);
                }
            }
        }

        $this->addFlash('notice', $nbSpeakers . ' conférenciers ont été ajoutés dans les inscriptions');

        return $this->redirectToRoute('admin_speaker_list', [
            'eventId' => $event->getId(),
        ]);
    }
}
