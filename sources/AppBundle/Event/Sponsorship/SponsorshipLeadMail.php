<?php

declare(strict_types=1);

namespace AppBundle\Event\Sponsorship;

use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Lead;
use AppBundle\Event\Model\Repository\EventRepository;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SponsorshipLeadMail
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger,
        private readonly EventRepository $eventRepository,
    ) {}

    /**
     * Dossier envoyé depuis la page générique, quelle que soit la langue choisie, et déposé manuellement sur le serveur.
     */
    public static function getSponsorshipFilePath(): string
    {
        return Event::getSponsorFileDir() . 'dossier-sponsoring-afup.pdf';
    }

    public function hasSponsorshipFile(): bool
    {
        return is_file(self::getSponsorshipFilePath());
    }

    public function sendSponsorshipFile(Lead $lead): void
    {
        $leadEvent = $lead->getEvent();
        $file = $leadEvent instanceof Event
            ? Event::getSponsorFilePath($leadEvent->getPath(), $lead->getLanguage())
            : self::getSponsorshipFilePath();
        $filename = basename($file);
        $event = $this->getSingleUpcomingEvent();

        if ($event === null) {
            $subject = $this->translator->trans('mail.sponsoringfile.title.generic');
            $content = $this->translator->trans('mail.sponsoringfile.text.generic');

            // Le dossier d'un évènement est renommé pour ne pas trahir le nom de l'évènement
            if ($leadEvent instanceof Event) {
                $filename = sprintf('dossier-sponsoring-afup-%s.pdf', $lead->getLanguage());
            }
        } else {
            $subject = $this->translator->trans('mail.sponsoringfile.title', ['%eventName%' => $event->getTitle()]);
            $content = $this->translator->trans('mail.sponsoringfile.text', ['%eventName%' => $event->getTitle()]);
        }

        $message = new Message($subject, MailUserFactory::sponsors(), new MailUser($lead->getEmail(), $lead->getLabel()));

        $message->addAttachment(new Attachment(
            $file,
            $filename,
            'base64',
            'application/pdf',
        ));

        if (!$this->mailer->sendTransactional($message, $content)) {
            $this->logger->warning(sprintf('Mail not sent for sponsorship lead retrieval: "%s"', $lead->getEmail()));
        }
    }

    /**
     * Le dossier de sponsoring couvre tous les évènements à venir : il n'est
     * nommé que lorsqu'un seul évènement est à venir.
     */
    private function getSingleUpcomingEvent(): ?Event
    {
        $events = $this->eventRepository->getNextEvents();

        if ($events === null || $events->count() !== 1) {
            return null;
        }

        return $events->first();
    }
}
