<?php

declare(strict_types=1);

namespace AppBundle\Association\PasswordReset;

use AppBundle\Association\Model\User;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class PasswordResetMailer
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly RouterInterface $router,
        private readonly Environment $twig,
    ) {}

    public function sendResetLink(User $user, string $plainToken, int $validityInMinutes): bool
    {
        $message = new Message(
            'AFUP : réinitialisation de votre mot de passe',
            MailUserFactory::bureau(),
            new MailUser($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName()),
        );

        // Message en texte brut : le lien doit rester intact et cliquable quel que
        // soit le client de messagerie.
        $message->setContent($this->twig->render('mail_templates/password_reset.txt.twig', [
            'login' => $user->getUsername(),
            'url' => $this->router->generate(
                'app_password_reset_confirm',
                ['token' => $plainToken],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
            'validity_in_minutes' => $validityInMinutes,
        ]));

        return $this->mailer->send($message);
    }
}
