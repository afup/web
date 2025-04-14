<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Cotisations;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserService
{
    private UserRepository $userRepository;
    private Mailer $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private string $sender = MailUser::DEFAULT_SENDER_EMAIL;
    private Cotisations $cotisations;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        Mailer $mailer,
        UrlGeneratorInterface $urlGenerator,
        Cotisations $cotisations,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->cotisations = $cotisations;
        $this->passwordHasher = $passwordHasher;
    }

    public function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(8));
    }

    public function resetPassword(User $user): void
    {
        $newPassword = $this->generateRandomPassword();
        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $this->userRepository->save($user);

        $login = $user->getUsername();
        $url = $this->urlGenerator->generate('admin_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $message = new Message(
            'AFUP : Mot de passe perdu ?',
            new MailUser($this->sender),
            new MailUser($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName())
        );
        $message->setContent(<<<BODY
Votre nouveau mot de passe est indiqué ci-dessous.
Il vous permettra de vous connecter dès maintenant au site de l'AFUP.

Votre identifiant : $login
Votre mot de passe : $newPassword

$url
BODY
        );
        $this->mailer->send($message);
    }

    /**
     * @param string $email
     */
    public function resetPasswordForEmail($email): void
    {
        $user = $this->userRepository->loadUserByEmailOrAlternateEmail($email);
        if (null !== $user) {
            $this->resetPassword($user);
        }
    }

    public function sendWelcomeEmail(User $user): bool
    {
        $message = new Message(
            'Votre compte afup.org',
            MailUserFactory::bureau(),
            new MailUser($user->getEmail(), sprintf('%s %s', $user->getFirstName(), $user->getLastName()))
        );
        $this->mailer->renderTemplate($message, 'mail_templates/confirmation_creation_compte.html.twig', [
            'login' => $user->getUsername(),
            'adresse' => MailUserFactory::afup()->getEmail(),
            'ville' => '',
        ]);

        return $this->mailer->send($message);
    }

    /**
     * @return array
     */
    public function getLastSubscription(User $user)
    {
        if ($user->getCompanyId()) {
            $id = $user->getCompanyId();
            $personType = AFUP_PERSONNES_MORALES;
        } else {
            $id = $user->getId();
            $personType = AFUP_PERSONNES_PHYSIQUES;
        }

        return $this->cotisations->obtenirDerniere($personType, $id);
    }
}
