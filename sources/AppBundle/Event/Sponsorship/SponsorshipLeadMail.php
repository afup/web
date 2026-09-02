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
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SponsorshipLeadMail
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger,
    ) {}

    public function sendSponsorshipFile(Lead $lead): void
    {
        $file = Event::getSponsorFilePath($lead->getEvent()->getPath(), $lead->getLanguage());
        $subject = $this->translator->trans('mail.sponsoringfile.title');
        $message = new Message($subject, MailUserFactory::sponsors(), new MailUser($lead->getEmail(), $lead->getLabel()));

        $message->addAttachment(new Attachment(
            $file,
            sprintf('dossier-sponsoring-afup-%s.pdf', $lead->getLanguage()),
            'base64',
            'application/pdf',
        ));

        $content = $this->translator->trans('mail.sponsoringfile.text');

        if (!$this->mailer->sendTransactional($message, $content)) {
            $this->logger->warning(sprintf('Mail not sent for sponsorship lead retrieval: "%s"', $lead->getEmail()));
        }
    }
}
