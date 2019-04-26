<?php

namespace AppBundle\Event\Ticket;

use Afup\Site\Utils\Mail;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\SponsorTicket;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SponsorTokenMail
{
    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(Mail $mail, TranslatorInterface $translator, RouterInterface $router, EventRepository $eventRepository)
    {
        $this->mail = $mail;
        $this->translator = $translator;
        $this->router = $router;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Send mail to a sponsor with a custom token to get tickets
     *
     * @param SponsorTicket $sponsorTicket
     * @param $lastCall boolean
     * @return bool
     */
    public function sendNotification(SponsorTicket $sponsorTicket, $lastCall = false)
    {
        /**
         * @var $event Event
         */
        $event = $this->eventRepository->get($sponsorTicket->getIdForum());


        $textLabel = 'mail.sponsorTicket.text';
        $subjectLabel = "mail.sponsorTicket.subject";

        if ($lastCall === true) {
            $textLabel = 'mail.sponsorTicketLastCall.text';
            $subjectLabel = "mail.sponsorTicketLastCall.subject";
        }

        $text = $this->translator->transChoice(
            $textLabel,
            $sponsorTicket->getMaxInvitations(),
            [
                '%token%' => $sponsorTicket->getToken(),
                '%places%' => $sponsorTicket->getMaxInvitations(),
                '%event%' => $event->getTitle(),
                '%link%' => $this->router->generate(
                    'sponsor_ticket_home',
                    ['eventSlug' => $event->getPath()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                '%endDate%' => $event->getDateEndSales()->format('d/m/Y')
            ]
        );

        return $this->mail->send(
            'message-transactionnel-afup-org',
            ['email' => $sponsorTicket->getContactEmail()],
            [
                'content' => $text,
                'title' => $this->translator->trans($subjectLabel, ['%event%' => $event->getTitle()])
            ],
            [
                'subject' => $this->translator->trans($subjectLabel, ['%event%' => $event->getTitle()]),
                'force_bcc' => true,
            ]
        );
    }
}
