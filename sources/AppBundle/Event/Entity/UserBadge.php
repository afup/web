<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\UserBadgeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBadgeRepository::class)]
#[ORM\Table(name: 'afup_personnes_physiques_badge')]
class UserBadge
{
    #[ORM\Id]
    #[ORM\Column(name: 'afup_personne_physique_id')]
    public int $userId;

    #[ORM\Id]
    #[ORM\Column(name: 'badge_id')]
    public int $badgeId;

    #[ORM\Column(name: 'issued_at', type: 'date_immutable')]
    public \DateTimeImmutable $issuedAt;
}
