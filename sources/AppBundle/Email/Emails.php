<?php

namespace AppBundle\Email;

use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use InvalidArgumentException;

class Emails
{
    /** @var Mailer */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendInscription(Event $event, MailUser $recipient)
    {
        $mailContent = $event->getMailInscriptionContent();

        if ('' === trim($mailContent)) {
            throw new InvalidArgumentException("Contenu du mail d'inscription non trouvé pour le forum " . $event->getTitle());
        }

        $message = new Message(sprintf('[%s] Merci !', $event->getTitle()), MailUserFactory::afup(), $recipient);
        $message->addBcc(MailUserFactory::tresorier());
        $this->mailer->renderTemplate($message, ':admin/event:mail_inscription.html.twig', [
            'content' => $mailContent,
            'logo_url' => $event->getLogoUrl(),
        ]);
        $this->mailer->send($message);
    }
}
