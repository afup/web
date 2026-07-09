<?php

declare(strict_types=1);

namespace AppBundle\Tests\Security;

use AppBundle\Security\LegacyHasher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LegacyHasherTest extends TestCase
{
    private const PASSWORD_ALGO = PASSWORD_ARGON2ID;

    #[DataProvider('verifyDataProvider')]
    public function testVerify(string $plain, string $hash, bool $expectedResult): void
    {
        $hasher = new LegacyHasher();

        self::assertSame($expectedResult, $hasher->verify($hash, $plain));
    }

    public static function verifyDataProvider(): \Generator
    {
        $plainPassword = 'super mot de passe';
        $md5Password = md5($plainPassword);
        $wrappedPassword = LegacyHasher::MD5_WRAPPED_PREFIX . password_hash($md5Password, self::PASSWORD_ALGO);

        yield 'md5 valid' => [
            'plain' => $plainPassword,
            'hash' => $md5Password,
            'expectedResult' => true,
        ];

        yield 'md5 invalid' => [
            'plain' => 'wrong',
            'hash' => $md5Password,
            'expectedResult' => false,
        ];

        yield 'wrapped md5 valid' => [
            'plain' => $plainPassword,
            'hash' => $wrappedPassword,
            'expectedResult' => true,
        ];

        yield 'wrapped md5 invalid' => [
            'plain' => 'wrong',
            'hash' => $wrappedPassword,
            'expectedResult' => false,
        ];

        yield 'empty md5' => [
            'plain' => '',
            'hash' => $md5Password,
            'expectedResult' => false,
        ];

        yield 'empty wrapped md5' => [
            'plain' => '',
            'hash' => $wrappedPassword,
            'expectedResult' => false,
        ];
    }

    #[DataProvider('needsRehashDataProvider')]
    public function testNeedsRehash(string $hash, bool $expectedResult): void
    {
        $hasher = new LegacyHasher();

        self::assertSame($expectedResult, $hasher->needsRehash($hash));
    }

    public static function needsRehashDataProvider(): \Generator
    {
        yield 'md5' => [
            'hash' => md5('secret'),
            'expectedResult' => true,
        ];

        yield 'wrapped md5' => [
            'hash' => LegacyHasher::MD5_WRAPPED_PREFIX . password_hash(md5('secret'), self::PASSWORD_ALGO),
            'expectedResult' => true,
        ];

        yield 'sodium' => [
            'hash' => password_hash('secret', self::PASSWORD_ALGO),
            'expectedResult' => false,
        ];
    }
}
