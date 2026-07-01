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
    public int $id;

    #[ORM\Column(type: UnixTimestampType::NAME)]
    public \DateTime $date;

    #[ORM\Column(name: 'label', length: 255, nullable: true)]
    public ?string $texte = null;

    #[ORM\Column(name: 'opened_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateOuverture {
        set(?\DateTime $dateOuverture) {
            $this->dateOuverture = $dateOuverture;
            if ($dateOuverture !== null) {
                $this->dateCreation ??= $dateOuverture;
            }
        }
    }

    #[ORM\Column(name: 'closed_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateCloture {
        set(?\DateTime $dateCloture) {
            $this->dateCloture = $dateCloture;
            if ($dateCloture !== null) {
                $this->dateCreation ??= $dateCloture;
            }
        }
    }

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    public ?\DateTime $dateCreation = null;

    /**
     * État calculé à partir de dateOuverture/dateCloture (et non stocké) : il doit
     * rester correct après hydratation Doctrine, qui ne déclenche pas les hooks `set`.
     */
    public QuestionEtat $etat {
        get => match (true) {
            ($this->dateCloture ?? null) !== null => QuestionEtat::Fermee,
            ($this->dateOuverture ?? null) !== null => QuestionEtat::Ouverte,
            default => QuestionEtat::EnAttente,
        };
    }

    /**
     * @param array<string, int> $results
     */
    public function hasVotes(array $results): bool
    {
        return 0 < $results[VoteValeur::Oui->value] + $results[VoteValeur::Non->value] + $results[VoteValeur::Abstention->value];
    }

    public function hasStatusWaiting(): bool
    {
        return $this->etat === QuestionEtat::EnAttente;
    }

    public function hasStatusOpened(): bool
    {
        return $this->etat === QuestionEtat::Ouverte;
    }

    public function hasStatusClosed(): bool
    {
        return $this->etat === QuestionEtat::Fermee;
    }
}
