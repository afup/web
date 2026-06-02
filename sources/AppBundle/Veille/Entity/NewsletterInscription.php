<?php

declare(strict_types=1);

namespace AppBundle\Veille\Entity;

use AppBundle\Association\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_techletter_subscriptions')]
class NewsletterInscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    public ?Utilisateur $user = null;

    #[ORM\Column(name: 'subscription_date', type: 'datetime', nullable: true)]
    public ?\DateTime $dateInscription = null;
}
