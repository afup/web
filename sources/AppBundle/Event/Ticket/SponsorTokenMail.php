<?php

declare(strict_types=1);

namespace AppBundle\Event\Ticket;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\SponsorTicket;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SponsorTokenMail
{
    private Mailer $mailer;

    private TranslatorInterface $translator;

    private RouterInterface $router;

    private EventRepository $eventRepository;

    public function __construct(Mailer $mail, TranslatorInterface $translator, RouterInterface $router, EventRepository $eventRepository)
    {
        $this->mailer = $mail;
        $this->translator = $translator;
        $this->router = $router;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Send mail to a sponsor with a custom token to get tickets
     *
     * @param $lastCall boolean
     */
    public function sendNotification(SponsorTicket $sponsorTicket, $lastCall = false): bool
    {
        /**
         * @var Event $event
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
                '%endDate%' => $event->getDateEndSalesSponsorToken()->format('d/m/Y')
            ]
        );

        return $this->mailer->sendTransactional(new Message(
            $this->translator->trans($subjectLabel, ['%event%' => $event->getTitle()]),
            MailUserFactory::afup(),
            new MailUser($sponsorTicket->getContactEmail(), $sponsorTicket->getCompany())
        ), $text);
    }
}
