<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer\Adapter;

use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use UnexpectedValueException;

final readonly class SymfonyMailerAdapter implements MailerAdapter
{
    public function __construct(private MailerInterface $mailer) {}

    public function send(Message $message): void
    {
        $from = $message->getFrom();
        if (!$from instanceof MailUser) {
            throw new UnexpectedValueException('Trying to send a mail with no sender');
        }

        $email = (new Email())
            ->from(new Address($from->getEmail(), $from->getName()))
            ->subject($message->getSubject());

        if ($message->isHtml()) {
            $email->html($message->getContent());
        } else {
            $email->text($message->getContent());
        }

        foreach ($message->getRecipients() as $mailUser) {
            $email->addTo(new Address($mailUser->getEmail(), $mailUser->getName()));
        }

        foreach ($message->getCc() as $mailUser) {
            $email->addCc(new Address($mailUser->getEmail(), $mailUser->getName()));
        }

        foreach ($message->getBcc() as $mailUser) {
            $email->addBcc(new Address($mailUser->getEmail(), $mailUser->getName()));
        }

        foreach ($message->getAttachments() as $attachment) {
            $email->attachFromPath(
                $attachment->getPath(),
                $attachment->getName(),
                $attachment->getType(),
            );
        }

        $this->mailer->send($email);
    }
}
