<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

use AppBundle\Email\Mailer\Adapter\MailerAdapter;
use Psr\Log\LoggerInterface;
use Twig\Environment;

/**
 * Send emails
 */
class Mailer
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment $twig,
        private readonly MailerAdapter $adapter,
    ) {}

    /**
     * @return boolean true on success, false on failure
     */
    public function send(Message $message): bool
    {
        try {
            if (!$message->getFrom() instanceof MailUser) {
                $message->setFrom(MailUserFactory::afup());
            }
            $this->adapter->send($message);

            return true;
        } catch (\Exception $e) {
            $this->logger->critical('Mail not sent: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * @param string      $content Contenu du mail
     * @param string|null $address Adresse e-mail de réponse
     * @param string|null $title   Titre du mail (par défaut la valeur du sujet du mail est utilisée)
     *
     * @return boolean true on success, false on failure
     */
    public function sendTransactional(Message $message, $content, $address = null, $title = null): bool
    {
        $this->renderTemplate($message, 'mail_templates/message-transactionnel.html.twig', [
            'title' => $title ?: $message->getSubject(),
            'content' => $content,
            'address' => $address ?: MailUserFactory::afup()->getEmail(),
        ]);

        return $this->send($message);
    }

    /**
     * Send simple message
     *
     * @param string $subject
     * @param string $content
     */
    public function sendSimpleMessage($subject, $content, MailUser ...$recipients): bool
    {
        if ([] === $recipients) {
            $recipients = [MailUserFactory::tresorier()];
        }
        $message = new Message($subject, null, ...$recipients);
        $message->setContent($content);

        return $this->send($message);
    }

    /**
     * @param string $template
     */
    public function renderTemplate(Message $message, $template, array $data): void
    {
        $message->setHtml();
        $message->setContent($this->twig->render($template, $data));
    }
}
