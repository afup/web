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
     * @var Mail
     */
    private $mail;

    /**
     * @param Mail $mail Mailer
     */
    public function __construct(Mail $mail)
    {
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

        $subject = sprintf("[%s] Merci !", $event->getTitle());

        $to =  [
            [
                'email' => $receiverEmail,
                'name' => $receiverLabel,
            ]
        ];

        $this->mail->send(':admin/event:mail_inscription.html.twig', $to, ['content' => $mailContent, 'logo_url' => $event->getLogoUrl()], [
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
