<?php

namespace AppBundle\Email;

use Afup\Site\Utils\Mail;
use AppBundle\Event\Model\Event;

class Emails
{
    const EMAIL_BUREAU_ADDRESS = 'bureau@afup.org';
    const EMAIL_BUREAU_LABEL = 'Bureau AFUP';
    const EMAIL_BONJOUR_ADDRESS = 'bonjour@afup.org';
    const EMAIL_BONJOUR_LABEL = 'AFUP';
    const EMAIL_TRESORIER_ADDRESS = 'tresorier@afup.org';

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

        $to =  [
            [
                'email' => $receiverEmail,
                'name' => $receiverLabel,
            ]
        ];

        $this->mail->send($content, $to, [], [
            'from' => [
                'email' => self::EMAIL_BONJOUR_ADDRESS,
                'name' => self::EMAIL_BONJOUR_LABEL,
            ],
            'force_bdd' => true,
            'bcc_address' => self::EMAIL_TRESORIER_ADDRESS,
            'subject' => $subject,
        ]);
    }
}
