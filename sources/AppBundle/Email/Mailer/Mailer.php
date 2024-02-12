<?php

namespace AppBundle\Email\Mailer;

use Afup\Site\Utils\Configuration;
use AppBundle\Email\Mailer\Adapter\MailerAdapter;
use Psr\Log\LoggerInterface;
use Twig_Environment;

/**
 * Send emails
 */
class Mailer
{
    /** @var LoggerInterface */
    private $logger;
    /** @var Twig_Environment */
    private $twig;
    /** @var MailerAdapter */
    private $adapter;
    /** @var string|null */
    private $forcedRecipient;
    /** @var string[] */
    private $defaultBccs;

    public function __construct(
        LoggerInterface $logger,
        Twig_Environment $twig,
        MailerAdapter $adapter,
        Configuration $configuration
    ) {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->adapter = $adapter;
        $this->forcedRecipient = $configuration->obtenir('mailer_force_recipients');
        $defaultBccs = $configuration->obtenir('mailer_bcc');
        $this->defaultBccs = is_array($defaultBccs) ? $defaultBccs : [$defaultBccs];
    }

    /**
     * @return boolean true on success, false on failure
     */
    public function send(Message $message, $addDefaultBccs = false)
    {
        try {
            if (null === $message->getFrom()) {
                $message->setFrom(MailUserFactory::afup());
            }
            if ($this->forcedRecipient) {
                $message->addRecipient(new MailUser($this->forcedRecipient));
            }
            if ($addDefaultBccs) {
                foreach ($this->defaultBccs as $bcc) {
                    $message->addBcc(new MailUser($bcc));
                }
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
    public function sendTransactional(Message $message, $content, $address = null, $title = null)
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
     *
     * @return bool
     */
    public function sendSimpleMessage($subject, $content, MailUser ...$recipients)
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
     * @param array  $data
     */
    public function renderTemplate(Message $message, $template, array $data)
    {
        $message->setHtml();
        $message->setContent($this->twig->render($template, $data));
    }
}
