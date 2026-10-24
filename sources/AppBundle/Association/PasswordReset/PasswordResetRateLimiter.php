<?php

declare(strict_types=1);

namespace AppBundle\Association\PasswordReset;

use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * Limitation des demandes de réinitialisation de mot de passe.
 *
 * Deux limiteurs sont combinés : par IP, contre l'énumération et le spam massif,
 * et par email, pour qu'on ne puisse pas inonder la boîte d'un membre depuis
 * plusieurs adresses.
 */
class PasswordResetRateLimiter
{
    public function __construct(
        private readonly RateLimiterFactoryInterface $passwordResetIpLimiter,
        private readonly RateLimiterFactoryInterface $passwordResetEmailLimiter,
    ) {}

    public function allows(?string $clientIp, string $email): bool
    {
        // Les deux limiteurs sont consommés systématiquement, y compris pour un
        // email inconnu : un comportement différent serait observable.
        $ipAccepted = $this->passwordResetIpLimiter
            ->create($clientIp)
            ->consume(1)
            ->isAccepted();

        $emailAccepted = $this->passwordResetEmailLimiter
            ->create($this->emailKey($email))
            ->consume(1)
            ->isAccepted();

        return $ipAccepted && $emailAccepted;
    }

    /**
     * L'email est normalisé (sinon la casse suffit à contourner la limite) et
     * haché (pour ne pas écrire d'adresses en clair dans le cache).
     */
    private function emailKey(string $email): string
    {
        return hash('xxh128', mb_strtolower(trim($email)));
    }
}
