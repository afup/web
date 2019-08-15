<?php

namespace Afup\Site\Utils;


use Exception;
use Psr\Log\LoggerInterface;

require_once dirname(__FILE__) . '/Configuration.php';

/**
 * Send emails via PHPMailer and SMTP
 */
class Mail
{

    const TEMPLATE_TRANSAC = 'message-transactionnel-afup-org';

    private $logger;
    private $twig;
    private $configuration;

    /**
     * Init the object by getting the Maindrill API key
     * @param $logger LoggerInterface
     * @param $twig Environnement Twig
     */
    public function __construct(LoggerInterface $logger, \Twig_Environment $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->configuration = new Configuration(dirname(__FILE__) . '/../../../configs/application/config.php');
    }

    /**
     * Retrieve the configuration
     * @throws Exception
     * @return Configuration
     */
    protected function _getConfig()
    {
        return $this->configuration;
    }

    /**
     * Send an email
     *
     * @param string $templateFile HTML content
     * @param array $receiver Receiver's data like ['email' => 'foo@bar.baz', 'name' => 'John Doe']
     * @param array $data Data to put in the email
     * @param array $parameters Some parameters (like bcc, etc.)
     * @return bool TRUE on success, FALSE on failure
     */
    public function send($templateFile, array $receiver, array $data = [], array $parameters = [])
    {
        $mailer = $this->getMailer();

        if(file_exists($templateFile)) {
            $content = $this->twig->render($templateFile, $data);
            $mailer->isHTML(true);
        } else {
            $content = $templateFile;
        }

        foreach ($receiver as $rec) {
            $mailer->AddAddress($rec['email'], $rec['name']);
        }

        if(array_key_exists('to', $parameters)) {
            foreach ($parameters['to'] as $rec) {
                $mailer->AddAddress($rec['email'], $rec['name']);
            }
        }

        $otherRecipients = $this->configuration->obtenir('mails|force_destinataire');
        if ($otherRecipients) {
            $mailer->AddAddress($otherRecipients);
        }


        if(array_key_exists('forceBcc', $parameters) && $parameters['forceBcc']) {
            $bcc = $this->configuration->obtenir('mails|bcc');
            if ($bcc) {
                $mailer->AddBCC($bcc);
            }
        }

        $mailer->From = $parameters['from']['email'];
        $mailer->FromName = $parameters['from']['name'];
        $mailer->Subject = $parameters['subject'];
        $mailer->Body = $content;
        return $mailer->Send();
    }

    /**
     * Send simple message
     * @param string $subject
     * @param string $message
     * @param array|null $receivers Array of receivers (['email', 'name']) or NULL to use default
     * @return bool
     */
    public function sendSimpleMessage($subject, $message, $receivers = null)
    {
        $parameters = array(
            'subject' => $subject,
            'bcc_address' => false,
        );
        if (is_array($receivers)) {
            $parameters['to'] = $receivers;
        } else {
            $parameters['to'] = array(array(
                'email' => 'tresorier@afup.org',
                'name' => 'Trésorier',
            ));
        }

        return $this->send($message, [], [], $parameters);
    }

    private function getMailer()
    {
        $mailer = new \PHPMailer();
        $mailer->CharSet = "utf-8";
        if ($this->configuration->obtenir('mails|serveur_smtp')) {
            $mailer->IsSMTP();
            $mailer->Host = $this->configuration->obtenir('mails|serveur_smtp');
            $mailer->SMTPAuth = false;
        }
        if ($this->configuration->obtenir('mails|tls') == true) {
            $mailer->SMTPAuth = $this->configuration->obtenir('mails|tls');
            $mailer->SMTPSecure = 'tls';
        }
        if ($this->configuration->obtenir('mails|username')) {
            $mailer->Username = $this->configuration->obtenir('mails|username');
        }
        if ($this->configuration->obtenir('mails|password')) {
            $mailer->Password = $this->configuration->obtenir('mails|password');
        }
        if ($this->configuration->obtenir('mails|port')) {
            $mailer->Port = $this->configuration->obtenir('mails|port');
        }
        return $mailer;
    }
}

