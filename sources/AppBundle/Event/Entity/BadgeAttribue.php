<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

/**
 * Badge attribué à un membre, avec les informations du badge portées par
 * la table afup_badge (encore gérée par l'entité Ting AppBundle\Event\Model\Badge).
 */
final readonly class BadgeAttribue
{
    public function __construct(
        public int $userId,
        public int $badgeId,
        public string $badgeLabel,
        public \DateTimeImmutable $issuedAt,
    ) {}
}
