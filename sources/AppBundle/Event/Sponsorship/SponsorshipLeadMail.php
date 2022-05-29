<?php

namespace AppBundle\Event\Sponsorship;

use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Lead;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SponsorshipLeadMail
{
    /**
     * @var Mailer
     */
    private $mailer;

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
        Mailer $mailer,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function sendSponsorshipFile(Lead $lead)
    {
        $file = Event::getSponsorFilePath($lead->getEvent()->getPath(), $lead->getLanguage());
        $subject = $this->translator->trans('mail.sponsoringfile.title', ['%eventName%' => $lead->getEvent()->getTitle()]);
        $message = new Message($subject, MailUserFactory::sponsors(), new MailUser($lead->getEmail(), $lead->getLabel()));

        $message->addAttachment(new Attachment(
            $file,
            basename($file),
            'base64',
            'application/pdf'
        ));

        $content = $this->translator->trans('mail.sponsoringfile.text', ['%eventName%' => $lead->getEvent()->getTitle()]);

        if (!$this->mailer->sendTransactional($message, $content)) {
            $this->logger->warning(sprintf('Mail not sent for sponsorship lead retrieval: "%s"', $lead->getEmail()));
        }
    }
}
