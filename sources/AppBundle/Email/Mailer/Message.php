<?php

namespace AppBundle\Email\Mailer;

class Message
{
    /** @var MailUser|null */
    private $from;
    /** @var MailUser[] */
    private $recipients;
    /** @var MailUser[] */
    private $cc = [];
    /** @var MailUser[] */
    private $bcc = [];
    /** @var string */
    private $subject;
    /** @var string */
    private $content = '';
    /** @var bool */
    private $isHtml = false;
    /** @var Attachment[] */
    private $attachments = [];

    /**
     * @param string $subject
     */
    public function __construct($subject, MailUser $from = null, MailUser ...$recipients)
    {
        $this->subject = $subject;
        $this->from = $from;
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    public function addRecipient(MailUser $user)
    {
        $this->recipients[$user->getEmail()] = $user;
    }

    public function addCc(MailUser $user)
    {
        $this->cc[$user->getEmail()] = $user;
    }

    public function addBcc(MailUser $user)
    {
        $this->bcc[$user->getEmail()] = $user;
    }

    public function setFrom(MailUser $user)
    {
        $this->from = $user;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param bool $isHtml
     */
    public function setHtml($isHtml = true)
    {
        $this->isHtml = $isHtml;
    }

    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;
    }

    /**
     * @return MailUser|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return MailUser[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @return MailUser[]
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @return MailUser[]
     */
    public function getBcc()
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
    public function getAttachments()
    {
        return $this->attachments;
    }
}
