<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Entity;

use AppBundle\AssembleeGenerale\Enum\QuestionEtat;
use AppBundle\AssembleeGenerale\Enum\VoteValeur;
use AppBundle\Doctrine\Type\UnixTimestampType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_assemblee_generale_question')]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: UnixTimestampType::NAME, nullable: true)]
    public ?\DateTime $date = null;

    #[ORM\Column(name: 'label', length: 255, nullable: true)]
    public ?string $texte = null;

    #[ORM\Column(name: 'opened_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateOuverture {
        set(?\DateTime $dateOuverture) {
            $this->dateOuverture = $dateOuverture;
            if ($dateOuverture !== null) {
                $this->dateCreation ??= $dateOuverture;
                $this->etat = QuestionEtat::Ouverte;
            }
        }
    }

    #[ORM\Column(name: 'closed_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateCloture {
        set(?\DateTime $dateCloture) {
            $this->dateCloture = $dateCloture;
            if ($dateCloture !== null) {
                $this->dateCreation ??= $dateCloture;
                $this->etat = QuestionEtat::Fermee;
            }
        }
    }

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateCreation = null;

    public QuestionEtat $etat = QuestionEtat::EnAttente;

    /**
     * @param array<string, int> $results
     */
    public function hasVotes(array $results): bool
    {
        return 0 < $results[VoteValeur::Oui->value] + $results[VoteValeur::Non->value] + $results[VoteValeur::Abstention->value];
    }
}
