<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Speaker;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Controller\Event\EventActionHelper;
use AppBundle\Event\Invoice\InvoiceService;
use AppBundle\Event\Model\Repository\InvoiceRepository;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TicketRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Ticket;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SpeakerRegisterAction
{
    use DbLoggerTrait;

    private SpeakerRepository $speakerRepository;
    private EventActionHelper $eventActionHelper;
    private TalkRepository $talkRepository;
    private TicketRepository $ticketRepository;
    private InvoiceService $invoiceService;
    private InvoiceRepository $invoiceRepository;
    private FlashBagInterface $flashBag;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SpeakerRepository $speakerRepository,
        EventActionHelper $eventActionHelper,
        TalkRepository $talkRepository,
        TicketRepository $ticketRepository,
        InvoiceService $invoiceService,
        InvoiceRepository $invoiceRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->speakerRepository = $speakerRepository;
        $this->eventActionHelper = $eventActionHelper;
        $this->talkRepository = $talkRepository;
        $this->ticketRepository = $ticketRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->invoiceService = $invoiceService;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $event = $this->eventActionHelper->getEventById($request->query->get('id'));
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);
        $nbSpeakers = 0;
        foreach ($talkAggregates as $talkAggregate) {
            $talk = $talkAggregate['talk'];
            if ($talk->getType() === Talk::TYPE_PHP_PROJECT) {
                continue;
            }
            $speakers = $this->speakerRepository->getSpeakersByTalk($talk);
            foreach ($speakers as $speaker) {
                $reference = 'GENCONF-' . $event->getId() . '-' . $speaker->getId() . '-' . Ticket::TYPE_SPEAKER;
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
                $ticket->setCivility($speaker->getCivility());
                $ticket->setLastname($speaker->getLastname());
                $ticket->setFirstname($speaker->getFirstname());
                $ticket->setEmail($speaker->getEmail());
                $ticket->setCompanyCitation(true);
                $ticket->setNewsletter(true);
                $ticket->setComments('import auto');
                $ticket->setStatus(Ticket::STATUS_GUEST);
                $ticket->setInvoiceStatus(Ticket::INVOICE_TODO);
                try {
                    $this->ticketRepository->save($ticket);
                } catch (Exception $e) {
                    $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de l\'inscription');

                    return new RedirectResponse($this->urlGenerator->generate('admin_speaker_list'));
                }
                try {
                    $this->invoiceService->handleInvoicing(
                        $reference,
                        Ticket::PAYMENT_NONE,
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
                        Ticket::STATUS_GUEST
                    );
                    $this->log('Ajout inscription conférencier ' . $speaker->getId());
                    $nbSpeakers++;
                } catch (Exception $e) {
                    $this->flashBag->add('error', 'Une erreur est survenue lors de l\'ajout de la facturation');

                    return new RedirectResponse($this->urlGenerator->generate('admin_speaker_list', ['eventId' => $event->getId()]));
                }
            }
        }
        $this->flashBag->add('notice', $nbSpeakers . ' conférenciers ont été ajoutés dans les inscriptions');

        return new RedirectResponse($this->urlGenerator->generate('admin_speaker_list', ['eventId' => $event->getId()]));
    }
}
