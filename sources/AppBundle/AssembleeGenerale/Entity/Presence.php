<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity;

use AppBundle\AssembleeGenerale\Enum\PresenceEtat;
use AppBundle\Association\Entity\Utilisateur;
use AppBundle\Doctrine\Entity;
use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_presences_assemblee_generale')]
class Presence extends Entity
{
    #[ORM\Column(type: UnixTimestampType::NAME, nullable: false)]
    public \DateTime $date;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_personne_physique', referencedColumnName: 'id', nullable: true)]
    public ?Utilisateur $utilisateur = null;

    #[ORM\Column(nullable: false, enumType: PresenceEtat::class)]
    public PresenceEtat $presence = PresenceEtat::EnAttente;
}
