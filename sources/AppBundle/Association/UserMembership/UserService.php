<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\MembershipFee\MembershipFeeService;
use AppBundle\Association\MemberType;
use AppBundle\Association\Model\User;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;
use AppBundle\MembershipFee\Entity\Cotisation;

class UserService
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly MembershipFeeService $membershipFeeService,
    ) {}

    public function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(8));
    }

    public function sendWelcomeEmail(User $user): bool
    {
        $message = new Message(
            'Votre compte afup.org',
            MailUserFactory::bureau(),
            new MailUser($user->getEmail(), sprintf('%s %s', $user->getFirstName(), $user->getLastName())),
        );
        $this->mailer->renderTemplate($message, 'mail_templates/confirmation_creation_compte.html.twig', [
            'login' => $user->getUsername(),
            'adresse' => MailUserFactory::afup()->getEmail(),
            'ville' => '',
        ]);

        return $this->mailer->send($message);
    }

    public function getLastSubscription(User $user): ?Cotisation
    {
        if ($user->getCompanyId()) {
            $id = $user->getCompanyId();
            $personType = MemberType::MemberCompany;
        } else {
            $id = $user->getId();
            $personType = MemberType::MemberPhysical;
        }

        return $this->membershipFeeService->getLatestByUserTypeAndId($personType, $id);
    }
}
