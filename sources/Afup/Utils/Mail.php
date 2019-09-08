<?php

namespace Afup\Site\Utils;


use Exception;
use Psr\Log\LoggerInterface;

/**
 * Send emails via PHPMailer and SMTP
 */
class Mail
{
    const TRANSACTIONAL_TEMPLATE_MAIL = ':mail_templates:message-transactionnel.html.twig';

    private $logger;
    private $twig;
    private $configuration;
    private $templateDirPath;

    /**
     * Init the object by getting LoggerInterface and TwigEnvironment
     * However getting null for old code for all parameters
     *
     * @param LoggerInterface|null $logger
     * @param Twig_Environnement|null $twig
     */
    public function __construct($logger, $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
        $this->configuration = new Configuration(dirname(__FILE__) . '/../../../configs/application/config.php');
        $this->templateDirPath = dirname(__FILE__) . '/../../../app/config/Resources/views/';
    }

    /**
     * Send an email
     *
     * @param string $templateFile HTML content
     * @param array $receiver Receiver's data like ['email' => 'foo@bar.baz', 'name' => 'John Doe']
     * @param array $data Data to put in the email
     * @param array $parameters Some parameters (like bcc, etc.)
     * @return bool TRUE on success, FALSE on failure
     * @throws Exception
     */
    public function send($templateFile, array $receiver, array $data = [], array $parameters = [])
    {
        $mailer = $this->getMailer();
        $isHtml = true;

        // Si on reçoit un template en paramètre, on appelle Twig avec ce template et les données 'data'
        // Dans le cas où Twig ne peut être utilisé, on appelle alors la méthode renderTemplateFile qui joue le rôle de Twig
        // pour générer le corps du mail sinon on envoie le contenu tel quel
        // TODO; A revoir lors du passage du site en full Symfony
        if (ends_with('.html.twig', $templateFile)) {
            if ($this->twig !== null) {
                $content = $this->twig->render($templateFile, $data);
            } else {
                $content = $this->renderTemplateFile($templateFile, $data);
            }
        } else {
            $content = $templateFile;
        }

        if (!array_key_exists('from', $parameters)) {
            $parameters['from'] = [
                'name' => 'AFUP',
                'email' => 'bonjour@afup.org'
            ];
        }

        foreach ($receiver as $rec) {
            $mailer->AddAddress($rec['email'], $rec['name']);
        }

        if (array_key_exists('to', $parameters)) {
            foreach ($parameters['to'] as $rec) {
                $mailer->AddAddress($rec['email'], $rec['name']);
            }
        }

        $otherRecipients = $this->configuration->obtenir('mails|force_destinataire');
        if ($otherRecipients) {
            $mailer->AddAddress($otherRecipients);
        }

        // Gestion des copies cachées
        if (array_key_exists('forceBcc', $parameters) && $parameters['forceBcc']) {
            $bcc = (array_key_exists('bcc_address', $parameters))
                        ? $parameters['bcc_address']
                        : $this->configuration->obtenir('mails|bcc');
            if (is_array($bcc)) {
                foreach($bcc as $bcc_email) {
                    $mailer->AddBCC($bcc_email);
                }
            } else {
                $mailer->AddBCC($bcc);
            }
        }

        if (array_key_exists('html', $parameters)) {
            $isHtml = $parameters['html'];
        }

        // Gestion des pièces jointes
        if (array_key_exists('attachments', $parameters)) {
            foreach($parameters['attachments'] as $attachment) {
                $mailer->AddAttachment($attachment['path'], $attachment['name'], $attachment['encoding'], $attachment['type']);
            }
        }

        $mailer->From = $parameters['from']['email'];
        $mailer->FromName = $parameters['from']['name'];
        $mailer->Subject = $parameters['subject'];
        $mailer->isHTML($isHtml);
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
        $parameters = [
            'subject' => $subject,
            'bcc_address' => false,
            'forceBcc' => false,
        ];
        if ($receivers && is_array($receivers)) {
            $parameters['to'] = $receivers;
        } else {
            $parameters['to'] = [
                [
                'email' => 'tresorier@afup.org',
                'name' => 'Trésorier']
            ];
        }

        return $this->send($message, [], [], $parameters);
    }

    /**
     * Génération et configuration de l'objet PHPMailer
     *
     * @return \PHPMailer objet mailer configuré
     */
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

    /**
     * Méthode permettant de récupérer le template et d'en modifier le contenu
     * A utiliser uniquement si le contexte Twig ne peut être utilisé
     * (cas des anciennes pages non basculées sur Symfony)
     *
     * @param string $templateName nom du template <repertoire>:<nom du fichier twig>
     * @param array $data contient les données à remplacer (clé => valeur)
     * @return string contenu modifié ou vide si erreur
     */
    private function renderTemplateFile($templateName, $data)
    {
        $content = '';

        list($templateDir, $templateFile) = explode(':', $templateName);
        $template = sprintf("%s%s/%s", $this->templateDirPath, $templateDir, $templateFile);

        // Chargement du template si le fichier existe
        if (file_exists($template)) {
            $content = file_get_contents($template);
            if (!empty($content)) {
                foreach ($data as $field => $value) {
                    // On remplace les clés entre {{ }} par leurs valeurs respectives
                    $content = preg_replace("/\{\{\s+$field\s+\}\}/", $value, $content);
                }
            }
        }

        return $content;
    }
}

