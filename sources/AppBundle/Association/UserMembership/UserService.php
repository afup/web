<?php

namespace AppBundle\Association\UserMembership;

use Afup\Site\Association\Cotisations;
use Afup\Site\Utils\Configuration;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserService
{
    /** @var UserRepository */
    private $userRepository;
    /** @var Mailer */
    private $mailer;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var string */
    private $sender;
    /** @var Cotisations */
    private $cotisations;

    public function __construct(
        UserRepository $userRepository,
        Mailer $mailer,
        UrlGeneratorInterface $urlGenerator,
        Configuration $configuration,
        Cotisations $cotisations
    ) {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->cotisations = $cotisations;
        $this->sender = $configuration->obtenir('mails|email_expediteur');
    }

    public function generateRandomPassword()
    {
        return substr(md5(uniqid(mt_rand(), true)), 0, 10);
    }

    public function resetPassword(User $user)
    {
        $newPassword = $this->generateRandomPassword();
        $user->setPlainPassword($newPassword);
        $this->userRepository->save($user);

        $login = $user->getUsername();
        $url = $this->urlGenerator->generate('admin_login');
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
    public function resetPasswordForEmail($email)
    {
        $user = $this->userRepository->loadUserByEmaiOrAlternateEmail($email);
        if (null !== $user) {
            $this->resetPassword($user);
        }
    }

    public function sendWelcomeEmail(User $user)
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
