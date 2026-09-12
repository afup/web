<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entité minimale en lecture seule sur la table legacy des inscriptions
 * (notamment pour le comptage en DQL du quota des billetteries privées).
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'afup_inscription_forum')]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(nullable: false)]
    public int $etat;

    #[ORM\Column(name: 'special_price_token', nullable: true)]
    public ?string $specialPriceToken = null;
}
