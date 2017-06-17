<?php

namespace AppBundle\Event\Sponsorship;

use Afup\Site\Utils\Mail;
use AppBundle\Event\Model\Lead;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SponsorshipLeadMail
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $sponsorshipFileDir;

    public function __construct(
        Mail $mail,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $sponsorshipFileDir
    ) {
        $this->mail = $mail;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->sponsorshipFileDir = $sponsorshipFileDir;
    }

    public function sendSponsorshipFile(Lead $lead)
    {
        $receiver = [
            'email' => $lead->getEmail(),
            'name'  => $lead->getLabel(),
        ];
        $filename = $lead->getEvent()->getPath() . '-sponsoring-' . $lead->getLanguage() . '.pdf';
        $filepath = $this->sponsorshipFileDir;

        $text = 'Bonjour,<br />
  <p>Vous avez demandé à recevoir le dossier de sponsoring pour le ' . $lead->getEvent()->getTitle() . ' </p>

  <p>Vous trouverez le dossier en pièce jointe. Pour toute demande de précisions ou devis,
  n\'hésitez pas à nous contacter par mail à l’adresse <a href="mailto:sponsors@afup.org">sponsors@afup.org</a>.</p>
  <p>À bientôt,<br />
  L’équipe sponsoring AFUP</p>

  <p>Suivez l’AFUP sur <a href="https://twitter.com/afup">Twitter</a> (@afup) et Facebook.</p>
  <p><a href="https://afup.org">afup.org</a>'
        ;

        $data = [
            'content' => $text,
            'title' => sprintf("Dossier de sponsoring %s", $lead->getEvent()->getTitle())
        ];

        $parameters = [
            'from_name' => 'AFUP sponsors',
            'from_email' => 'sponsors@afup.org',
            'attachments' => [
                [
                    'type' => 'application/pdf',
                    'name' => $lead->getEvent()->getPath() . '-sponsoring-' . $lead->getLanguage() . '.pdf',
                    'content' => base64_encode(file_get_contents($filepath . $filename)),
                ]
            ],
            'subject' => sprintf("Dossier de sponsoring %s", $lead->getEvent()->getTitle())
        ];

        if (!$this->mail->send(Mail::TEMPLATE_TRANSAC, $receiver, $data, $parameters)) {
            $this->logger->warning(sprintf('Mail not sent for sponsorship lead retrieval: "%s"', $lead->getEmail()));
        }
    }
}
