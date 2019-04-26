<?php

namespace Afup\Site\Utils;

use Psr\Log\LoggerInterface;

class Mail
{
    const TEMPLATE_TRANSAC = 'message-transactionnel-afup-org';

    /** @var Configuration */
    private $config;
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function send($template, array $receiver, array $data = [], array $parameters = [])
    {
        $vars = array_merge($receiver, $data);
        $mail = $this->getMailer($parameters);

        if (!isset($receiver['name'])) {
            $receiver['name'] = '';
        }

        if (!isset($parameters['subject'])) {
            $parameters['subject'] = '';
        }

        $mail->AddAddress($receiver['email'], $receiver['name']);
        $mail->Subject = $parameters['subject'];
        $mail->Body = $this->generateBody($template, $vars);

        try {
            $mail->Send();
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    public function sendSimpleMessage($subject, $message, $receivers = null)
    {
        if (!is_array($receivers)) {
            $receivers = [
                'email' => 'tresorier@afup.org',
                'name' => 'Trésorier',
            ];
        }

        $data = ['message' => $message];

        $parameters = [
            'subject' => $subject,
            'force_bcc' => true,
        ];

        return $this->send('message', $receivers, $data, $parameters);
    }

    private function generateBody($template, array $vars)
    {
        // TODO : Generate html from twig template
        $vars = print_r($vars, true);

        return "
            <p>Template: $template</p>
            <p>Vars: $vars</p>
        ";
    }

    private function getConfig()
    {
        if (!isset($this->config)) {
            $this->config = new Configuration(dirname(__FILE__).'/../../../configs/application/config.php');
        }

        return $this->config;
    }

    private function getMailer(array $parameters = [])
    {
        $mail = new \PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->IsHTML(true);

        if (!isset($parameters['force_bcc'])) {
            $parameters['force_bcc'] = false;
        }

        if ($this->getConfig()->obtenir('mails|serveur_smtp')) {
            $mail->IsSMTP();
            $mail->Host = $this->getConfig()->obtenir('mails|serveur_smtp');
            $mail->SMTPAuth = false;
        }

        if ($this->getConfig()->obtenir('mails|tls') == true) {
            $mail->SMTPAuth = $this->getConfig()->obtenir('mails|tls');
            $mail->SMTPSecure = 'tls';
        }

        if ($this->getConfig()->obtenir('mails|username')) {
            $mail->Username = $this->getConfig()->obtenir('mails|username');
        }

        if ($this->getConfig()->obtenir('mails|password')) {
            $mail->Password = $this->getConfig()->obtenir('mails|password');
        }

        if ($this->getConfig()->obtenir('mails|port')) {
            $mail->Port = $this->getConfig()->obtenir('mails|port');
        }

        if ($this->getConfig()->obtenir('mails|force_destinataire')) {
            $mail->AddAddress($this->getConfig()->obtenir('mails|force_destinataire'));
        }

        if ($this->getConfig()->obtenir('mails|email_expediteur')) {
            $mail->From = $this->getConfig()->obtenir('mails|email_expediteur');
        }

        if ($this->getConfig()->obtenir('mails|nom_expediteur')) {
            $mail->FromName = $this->getConfig()->obtenir('mails|nom_expediteur');
        }

        if ($this->getConfig()->obtenir('mails|bcc') && $parameters['force_bcc'] !== true) {
            $mail->AddBCC($this->getConfig()->obtenir('mails|bcc'));
        }

        if (!empty($parameters['bcc_address']) && $parameters['force_bcc'] === true) {
            $mail->AddBCC($parameters['bcc_address']);
        }

        if (!empty($parameters['from_email'])) {
            $mail->From = $parameters['from_email'];
        }

        if (!empty($parameters['from_name'])) {
            $mail->FromName = $parameters['from_name'];
        }

        return $mail;
    }
}

