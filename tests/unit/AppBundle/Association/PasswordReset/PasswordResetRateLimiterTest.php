<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\PasswordReset;

use AppBundle\Association\PasswordReset\PasswordResetRateLimiter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class PasswordResetRateLimiterTest extends TestCase
{
    public function testTheFourthRequestFromTheSameIpIsRejected(): void
    {
        $rateLimiter = new PasswordResetRateLimiter(
            $this->buildFactory('ip', 3),
            $this->buildFactory('email', 100),
        );

        // Des emails différents à chaque fois : seule l'IP peut faire tomber la limite.
        self::assertTrue($rateLimiter->allows('10.0.0.1', 'un@example.org'));
        self::assertTrue($rateLimiter->allows('10.0.0.1', 'deux@example.org'));
        self::assertTrue($rateLimiter->allows('10.0.0.1', 'trois@example.org'));
        self::assertFalse($rateLimiter->allows('10.0.0.1', 'quatre@example.org'));

        // Une autre IP n'est pas affectée.
        self::assertTrue($rateLimiter->allows('10.0.0.2', 'cinq@example.org'));
    }

    public function testTheFourthRequestForTheSameEmailIsRejected(): void
    {
        $rateLimiter = new PasswordResetRateLimiter(
            $this->buildFactory('ip', 100),
            $this->buildFactory('email', 3),
        );

        // Des IP différentes à chaque fois : seul l'email peut faire tomber la limite.
        self::assertTrue($rateLimiter->allows('10.0.0.1', 'victime@example.org'));
        self::assertTrue($rateLimiter->allows('10.0.0.2', 'victime@example.org'));
        self::assertTrue($rateLimiter->allows('10.0.0.3', 'victime@example.org'));
        self::assertFalse($rateLimiter->allows('10.0.0.4', 'victime@example.org'));
    }

    public function testTheEmailKeyIsNormalized(): void
    {
        $rateLimiter = new PasswordResetRateLimiter(
            $this->buildFactory('ip', 100),
            $this->buildFactory('email', 2),
        );

        // Casse et espaces ne doivent pas suffire à repartir d'un quota neuf.
        self::assertTrue($rateLimiter->allows('10.0.0.1', 'victime@example.org'));
        self::assertTrue($rateLimiter->allows('10.0.0.1', ' Victime@Example.org '));
        self::assertFalse($rateLimiter->allows('10.0.0.1', 'VICTIME@EXAMPLE.ORG'));
    }

    public function testAMissingClientIpIsStillLimited(): void
    {
        $rateLimiter = new PasswordResetRateLimiter(
            $this->buildFactory('ip', 1),
            $this->buildFactory('email', 100),
        );

        self::assertTrue($rateLimiter->allows(null, 'un@example.org'));
        self::assertFalse($rateLimiter->allows(null, 'deux@example.org'));
    }

    private function buildFactory(string $id, int $limit): RateLimiterFactoryInterface
    {
        return new RateLimiterFactory(
            ['id' => $id, 'policy' => 'sliding_window', 'limit' => $limit, 'interval' => '1 hour'],
            new InMemoryStorage(),
        );
    }
}
