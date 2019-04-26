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

        $data = [
            'content' => $this->translator->trans('mail.sponsoringfile.text', ['%eventName%' => $lead->getEvent()->getTitle()]),
            'title' => $this->translator->trans('mail.sponsoringfile.title', ['%eventName%' => $lead->getEvent()->getTitle()])
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
            'subject' => $this->translator->trans('mail.sponsoringfile.title', ['%eventName%' => $lead->getEvent()->getTitle()]),
            'force_bcc' => true,
        ];

        if (!$this->mail->send(Mail::TEMPLATE_TRANSAC, $receiver, $data, $parameters)) {
            $this->logger->warning(sprintf('Mail not sent for sponsorship lead retrieval: "%s"', $lead->getEmail()));
        }
    }
}
