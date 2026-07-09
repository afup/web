<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class LegacyHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    public const string MD5_WRAPPED_PREFIX = '$md5_wrapped$';

    public function hash(string $plainPassword): string
    {
        throw new \Exception('Hash en pur md5 désactivé');
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        if (str_starts_with($hashedPassword, self::MD5_WRAPPED_PREFIX)) {
            $argon2idPart = substr($hashedPassword, strlen(self::MD5_WRAPPED_PREFIX));

            return password_verify(md5($plainPassword), $argon2idPart);
        }

        return $hashedPassword === md5($plainPassword);
    }

    public function needsRehash(string $hashedPassword): bool
    {
        return strlen($hashedPassword) === 32 || str_starts_with($hashedPassword, self::MD5_WRAPPED_PREFIX);
    }
}
