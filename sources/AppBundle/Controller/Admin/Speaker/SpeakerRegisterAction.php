<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use AppBundle\AuditLog\Audit;
use AppBundle\Event\AdminEventSelection;
use AppBundle\Event\Invoice\InvoiceService;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Entity\Repository\InscriptionRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Entity\Inscription;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\InscriptionFacturation;
use AppBundle\Event\Enum\TypeInscription;
use AppBundle\Event\Enum\TypeReglement;

#[IsGranted('ROLE_EVENT_WRITER')]
class SpeakerRegisterAction extends AbstractController
{
    public function __construct(
        private readonly SpeakerRepository $speakerRepository,
        private readonly TalkRepository $talkRepository,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly InvoiceService $invoiceService,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly Audit $audit,
    ) {}

    public function __invoke(Request $request, AdminEventSelection $eventSelection): RedirectResponse
    {
        $event = $eventSelection->event;
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);
        $nbSpeakers = 0;

        foreach ($talkAggregates as $talkAggregate) {
            if ($talkAggregate->talk->getType() === Talk::TYPE_PHP_PROJECT) {
                continue;
            }

            /** @var array<Speaker> $speakers */
            $speakers = $this->speakerRepository->getSpeakersByTalk($talkAggregate->talk);

            foreach ($speakers as $speaker) {
                $reference = 'GENCONF-' . $event->getId() . '-' . $speaker->getId() . '-' . TypeInscription::Conferencier->value;
                $invoice = $this->invoiceRepository->getByReference($reference);

                if ($invoice) {
                    continue;
                }

                $ticket = new Inscription();
                $ticket->date = new DateTime();
                $ticket->amount = 0.0;
                $ticket->forumId = (int) $event->getId();
                $ticket->reference = $reference;
                $ticket->ticketTypeId = TypeInscription::Conferencier->value;
                $ticket->genre = $speaker->getGenre();
                $ticket->lastname = $speaker->getLastname();
                $ticket->firstname = $speaker->getFirstname();
                $ticket->email = $speaker->getEmail();
                $ticket->companyCitation = true;
                $ticket->newsletter = true;
                $ticket->comments = 'import auto';
                $ticket->status = InscriptionEtat::Invitation;
                $ticket->invoiceStatus = InscriptionFacturation::AEnvoyer;

                try {
                    $this->inscriptionRepository->save($ticket);
                } catch (Exception) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout de l\'inscription');

                    return $this->redirectToRoute('admin_speaker_list');
                }

                try {
                    $this->invoiceService->handleInvoicing(
                        $reference,
                        TypeReglement::Aucun->value,
                        '',
                        null,
                        $speaker->getEmail(),
                        $speaker->getCompany(),
                        $speaker->getLastname(),
                        $speaker->getFirstname(),
                        '',
                        '',
                        '',
                        'FR',
                        $event->getId(),
                        null,
                        '',
                        '',
                        InscriptionEtat::Invitation->value,
                    );
                    $this->audit->log('Ajout inscription conférencier ' . $speaker->getId());
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
