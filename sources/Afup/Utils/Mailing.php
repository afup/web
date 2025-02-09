<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use AppBundle\Email\Mailer\Message;

class Mailing
{
    /**
     * @param string $body
     */
    public static function envoyerMail(Message $message, $body): bool
    {
        $recipients = $message->getRecipients();
        $recipient = reset($recipients);
        $message->setContent(str_replace('$EMAIL$', $recipient->getEmail(), $body));

        return Mail::createMailer()->send($message);
    }
}
