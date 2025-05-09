<?php

declare(strict_types=1);

namespace AppBundle\Email;

use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use InvalidArgumentException;

class Emails
{
    private array $tempFiles = [];

    public function __construct(private readonly Mailer $mailer)
    {
    }

    public function __destruct()
    {
        // Suppression des fichiers temporaires
        foreach ($this->tempFiles as $file) {
            unlink($file);
        }
    }

    public function sendInscription(Event $event, MailUser $recipient): void
    {
        $mailContent = $event->getMailInscriptionContent();
        if (!$mailContent) {
            throw new InvalidArgumentException("Contenu du mail d'inscription non trouvé pour le forum " . $event->getTitle());
        }

        $eventPath = $event->getPath();

        $message = new Message(sprintf('[%s] Merci !', $event->getTitle()), MailUserFactory::afup(), $recipient);

        if (Event::hasInscriptionAttachment($eventPath)) {
            $message->addAttachment(new Attachment(Event::getInscriptionAttachmentFilepath($eventPath), $event->getTitle() . '.pdf', 'base64', 'application/pdf'));
        }

        $message->addAttachment($this->getAttachementIcsInscription($event, $recipient));

        $this->mailer->renderTemplate($message, 'admin/event/mail_inscription.html.twig', [
            'event' => $event,
            'recipient' => $recipient,
            'content' => $mailContent,
            'logo_url' => $event->getLogoUrl(),
        ]);
        $this->mailer->send($message);
    }

    private function getAttachementIcsInscription(Event $event, MailUser $recipient): Attachment
    {
        $uid = md5((string) $event->getId());
        $organizerCN = MailUserFactory::afup()->getName();
        $attendeeCN = $recipient->getName();
        $attendeeEmail = $recipient->getEmail();
        $created = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');

        $dtStart = $event->getDateStart()->format('Ymd');
        $dtEnd = $event->getDateEnd()->add(new \DateInterval('P1D'))->format('Ymd');

        $content = <<<EOF
BEGIN:VCALENDAR
PRODID:-//AFUP//AFUP 0.0.7//FR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART;VALUE=DATE:{$dtStart}
DTEND;VALUE=DATE:{$dtEnd}
DTSTAMP:{$created}
ORGANIZER:CN={$organizerCN}
ATTENDEE:CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;CN=
{$attendeeEmail};X-NUM-GUESTS=0:mailto:{$attendeeCN}
UID:{$uid}
CREATED:{$created}
LAST-MODIFIED:{$created}
DESCRIPTION:
LOCATION:{$event->getPlaceAddress()}
SEQUENCE:1
STATUS:CONFIRMED
SUMMARY:{$event->getTitle()}
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR
EOF;

        $path = tempnam(sys_get_temp_dir(), 'inscr');
        file_put_contents($path, str_replace("\n", "\r\n", $content));

        $this->tempFiles[] = $path;
        return new Attachment($path, $event->getTitle() . '.ics', 'base64', 'text/calendar');
    }
}
