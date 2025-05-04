<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

class Message
{
    /** @var MailUser[] */
    private ?array $recipients = null;
    /** @var MailUser[] */
    private array $cc = [];
    /** @var MailUser[] */
    private array $bcc = [];
    /** @var string */
    private $content = '';
    /** @var bool */
    private $isHtml = false;
    /** @var Attachment[] */
    private array $attachments = [];

    /**
     * @param string $subject
     */
    public function __construct(
        private $subject,
        private ?MailUser $from = null,
        MailUser ...$recipients,
    ) {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    public function addRecipient(MailUser $user): void
    {
        $this->recipients[$user->getEmail()] = $user;
    }

    public function addCc(MailUser $user): void
    {
        $this->cc[$user->getEmail()] = $user;
    }

    public function addBcc(MailUser $user): void
    {
        $this->bcc[$user->getEmail()] = $user;
    }

    public function setFrom(MailUser $user): void
    {
        $this->from = $user;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @param string $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param bool $isHtml
     */
    public function setHtml($isHtml = true): void
    {
        $this->isHtml = $isHtml;
    }

    public function addAttachment(Attachment $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    public function getFrom(): ?MailUser
    {
        return $this->from;
    }

    /**
     * @return MailUser[]
     */
    public function getRecipients(): ?array
    {
        return $this->recipients;
    }

    /**
     * @return MailUser[]
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return MailUser[]
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isHtml()
    {
        return $this->isHtml;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }
}
