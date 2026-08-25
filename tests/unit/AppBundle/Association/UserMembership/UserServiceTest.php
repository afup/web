<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\UserMembership;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\MembershipFee\MembershipFeeService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserServiceTest extends TestCase
{
    /**
     * Le dépôt lève une UserNotFoundException quand aucun compte ne correspond : la demande de
     * nouveau mot de passe doit s'arrêter silencieusement, sans révéler que l'email est inconnu.
     */
    #[Test]
    public function itIgnoresAPasswordResetForAnUnknownEmail(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->method('loadUserByEmailOrAlternateEmail')
            ->willThrowException(new UserNotFoundException());
        $userRepository->expects($this->never())->method('save');

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->never())->method('send');

        $userService = new UserService(
            $userRepository,
            $mailer,
            $this->createStub(UrlGeneratorInterface::class),
            $this->createStub(MembershipFeeService::class),
            $this->createStub(UserPasswordHasherInterface::class),
        );

        $userService->resetPasswordForEmail('aucun-compte@example.invalid');
    }
}
