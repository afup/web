<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_password_reset_tokens')]
class PasswordResetToken
{
    /**
     * Identifiant assigné : la clé primaire est l'utilisateur lui-même, ce qui
     * garantit qu'un seul jeton peut exister à un instant T pour un membre donné.
     */
    #[ORM\Id]
    #[ORM\Column(name: 'user_id')]
    public int $userId;

    #[ORM\Column(name: 'token_hash', length: 64, unique: true)]
    public string $tokenHash;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'expires_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $expiresAt;
}
