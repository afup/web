<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Association\PasswordReset;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use AppBundle\Association\PasswordReset\PasswordResetMailer;
use AppBundle\Association\PasswordReset\PasswordResetService;
use Doctrine\DBAL\Connection;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordResetServiceTest extends IntegrationTestCase
{
    /**
     * Tokens en clair envoyés par email, dans l'ordre d'envoi.
     *
     * @var list<string>
     */
    private array $sentTokens = [];

    protected function tearDown(): void
    {
        Clock::set(new NativeClock());

        parent::tearDown();
    }

    public function testOnlyTheHashOfTheTokenIsStored(): void
    {
        $service = $this->buildService();
        $user = $this->createUser();

        $service->createTokenFor($user);

        $plainToken = $this->lastSentToken();
        $row = $this->fetchTokenRows()[0];

        self::assertSame(hash('sha256', $plainToken), $row['token_hash']);
        self::assertStringNotContainsString($plainToken, implode('|', array_map(strval(...), $row)));
    }

    public function testTheTokenExpiresAfterTheConfiguredDelay(): void
    {
        Clock::set(new MockClock('2026-03-01 10:00:00'));

        $service = $this->buildService();
        $service->createTokenFor($this->createUser());
        $plainToken = $this->lastSentToken();

        Clock::set(new MockClock('2026-03-01 10:29:59'));
        self::assertNotNull($service->findValidToken($plainToken));

        Clock::set(new MockClock('2026-03-01 10:30:01'));
        self::assertNull($service->findValidToken($plainToken));
    }

    public function testASecondRequestReplacesThePendingToken(): void
    {
        $service = $this->buildService();
        $user = $this->createUser();

        $service->createTokenFor($user);
        $firstToken = $this->lastSentToken();

        $service->createTokenFor($user);
        $secondToken = $this->lastSentToken();

        // La clé primaire est l'utilisateur : une seule ligne, donc un seul token
        // valide à un instant T.
        self::assertCount(1, $this->fetchTokenRows());
        self::assertNull($service->findValidToken($firstToken));
        self::assertNotNull($service->findValidToken($secondToken));
    }

    public function testANewRequestPurgesExpiredTokens(): void
    {
        Clock::set(new MockClock('2026-03-01 10:00:00'));

        $service = $this->buildService();
        $service->createTokenFor($this->createUser());

        Clock::set(new MockClock('2026-03-01 11:00:00'));
        $otherUser = $this->createUser('autre-utilisateur', 'autre@example.org');
        $service->createTokenFor($otherUser);

        $rows = $this->fetchTokenRows();
        self::assertCount(1, $rows);
        self::assertSame($otherUser->getId(), (int) $rows[0]['user_id']);
    }

    public function testResettingChangesThePasswordAndConsumesTheToken(): void
    {
        $service = $this->buildService();
        $user = $this->createUser();

        $service->createTokenFor($user);
        $token = $service->findValidToken($this->lastSentToken());
        self::assertNotNull($token);

        self::assertTrue($service->reset($token, 'correcte cheval batterie agrafe'));

        self::assertSame([], $this->fetchTokenRows());
        self::assertNull($service->findValidToken($this->lastSentToken()));

        $passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $reloadedUser = self::getContainer()->get(UserRepository::class)->get($user->getId());
        self::assertInstanceOf(User::class, $reloadedUser);
        self::assertTrue($passwordHasher->isPasswordValid($reloadedUser, 'correcte cheval batterie agrafe'));
    }

    public function testAnUnknownEmailCreatesNoTokenAndSendsNoEmail(): void
    {
        $service = $this->buildService();
        $this->createUser();

        $service->requestResetForEmail('inconnu@example.org');

        self::assertSame([], $this->fetchTokenRows());
        self::assertSame([], $this->sentTokens);
    }

    public function testRequestingByEmailCreatesATokenForTheMatchingUser(): void
    {
        $service = $this->buildService();
        $user = $this->createUser();

        $service->requestResetForEmail('reset@example.org');

        self::assertCount(1, $this->sentTokens);
        $token = $service->findValidToken($this->lastSentToken());
        self::assertNotNull($token);
        self::assertSame($user->getId(), $token->userId);
    }

    private function buildService(): PasswordResetService
    {
        $mailer = $this->createStub(PasswordResetMailer::class);
        $mailer
            ->method('sendResetLink')
            ->willReturnCallback(function (User $user, string $plainToken): bool {
                $this->sentTokens[] = $plainToken;

                return true;
            });

        self::getContainer()->set(PasswordResetMailer::class, $mailer);

        return self::getContainer()->get(PasswordResetService::class);
    }

    private function createUser(string $login = 'utilisateur-reset', string $email = 'reset@example.org'): User
    {
        $connection = self::getContainer()->get(Connection::class);
        $connection->insert('afup_personnes_physiques', [
            'login' => $login,
            'mot_de_passe' => 'peu importe',
            'email' => $email,
            'nom' => 'Test',
            'prenom' => 'Utilisateur',
            'roles' => '["ROLE_USER"]',
            'adresse' => '1 rue du Test',
            'etat' => 1,
        ]);

        $user = self::getContainer()->get(UserRepository::class)->get((int) $connection->lastInsertId());
        self::assertInstanceOf(User::class, $user);

        return $user;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchTokenRows(): array
    {
        return self::getContainer()
            ->get(Connection::class)
            ->fetchAllAssociative('SELECT * FROM afup_password_reset_tokens');
    }

    private function lastSentToken(): string
    {
        self::assertNotSame([], $this->sentTokens);

        return $this->sentTokens[count($this->sentTokens) - 1];
    }
}
