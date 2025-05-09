<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer\Adapter;

use Afup\Site\Utils\Configuration;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\Message;
use PHPMailer\PHPMailer\PHPMailer;
use UnexpectedValueException;

class PhpMailerAdapter implements MailerAdapter
{
    public function __construct(
        private readonly ?string $smtpServer,
        private readonly ?string $tls,
        private readonly ?string $username,
        private readonly ?string $password,
        private readonly ?string $port,
    ) {
    }

    public static function createFromConfiguration(Configuration $configuration): self
    {
        return new self(
            $configuration->obtenir('smtp_host'),
            (string) $configuration->obtenir('smtp_tls'),
            $configuration->obtenir('smtp_username'),
            $configuration->obtenir('smtp_password'),
            (string) $configuration->obtenir('smtp_port')
        );
    }

    public function send(Message $message): void
    {
        $from = $message->getFrom();
        if (!$from instanceof MailUser) {
            throw new UnexpectedValueException('Trying to send a mail with no sender');
        }
        $phpMailer = $this->createPhpMailer();
        $phpMailer->setFrom($from->getEmail(), $from->getName());
        $phpMailer->isHTML($message->isHtml());
        $phpMailer->Subject = $message->getSubject();
        $phpMailer->Body = $message->getContent();
        foreach ($message->getRecipients() as $user) {
            $phpMailer->addAddress($user->getEmail(), $user->getName());
        }
        foreach ($message->getCc() as $user) {
            $phpMailer->addAddress($user->getEmail(), $user->getName());
        }
        foreach ($message->getBcc() as $user) {
            $phpMailer->addAddress($user->getEmail(), $user->getName());
        }
        foreach ($message->getAttachments() as $attachment) {
            $phpMailer->addAttachment($attachment->getPath(), $attachment->getName(), $attachment->getEncoding(), $attachment->getType());
        }

        $phpMailer->send();
    }

    /**
     * Génération et configuration de l'objet PHPMailer
     */
    private function createPhpMailer(): PHPMailer
    {
        // Exceptions gérées
        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'utf-8';
        if (null !== $this->smtpServer) {
            $mailer->IsSMTP();
            $mailer->Host = $this->smtpServer;
            $mailer->SMTPAuth = false;
        }
        if (null !== $this->tls && $this->tls !== '0') {
            $mailer->SMTPAuth = true;
            $mailer->SMTPSecure = 'tls';
        }
        if ($this->username) {
            $mailer->Username = $this->username;
        }
        if ($this->password) {
            $mailer->Password = $this->password;
        }
        if ($this->port) {
            $mailer->Port = (int) $this->port;
        }

        return $mailer;
    }
}
