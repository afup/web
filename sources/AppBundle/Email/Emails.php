<?php

namespace AppBundle\Email;

use Afup\Site\Utils\Mail;
use AppBundle\Event\Model\Event;

class Emails
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Mail
     */
    private $mail;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig, Mail $mail)
    {
        $this->twig = $twig;
        $this->mail = $mail;
    }

    /**
     * @param Event $event
     * @param string $receiverEmail
     * @param string $receiverLabel
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendInscription(Event $event, $receiverEmail, $receiverLabel)
    {
        $mailContent = $event->getMailInscriptionContent();

        if (0 === strlen(trim($mailContent))) {
            throw new \Exception("Contenu du mail d'inscription non trouvé pour le forum " . $event->getTitle());
        }

        $content = $this->twig->render(':admin/event:mail_inscription.html.twig', ['content' => $mailContent, 'logo_url' => $event->getLogoUrl()]);

        $subject = sprintf("[%s] Merci !", $event->getTitle());

        $this->mail->getMandrill()->messages->send([
            'from_email' => 'bonjour@afup.org',
            'from_name' => 'AFUP',
            'bcc_address' => 'tresorier@afup.org',
            'html' => $content,
            'subject' => $subject,
            'to' => [
                [
                    'email' => $receiverEmail,
                    'name' => $receiverLabel,
                ]
            ]
        ]);
    }
}
