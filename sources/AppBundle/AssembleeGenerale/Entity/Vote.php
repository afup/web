<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity;

use AppBundle\AssembleeGenerale\Enum\VoteValeur;
use AppBundle\Association\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_vote_assemblee_generale')]
class Vote
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(name: 'afup_assemblee_generale_question_id', referencedColumnName: 'id', nullable: false)]
    public Question $question;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'afup_personnes_physiques_id', referencedColumnName: 'id', nullable: false)]
    public Utilisateur $utilisateur;

    #[ORM\Column(name: 'weight')]
    public int $poids;

    #[ORM\Column(name: 'value', nullable: true)]
    public ?VoteValeur $valeur = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    public \DateTime $creeLe;

    public function getValueLabel(): ?string
    {
        return $this->valeur?->label();
    }
}
