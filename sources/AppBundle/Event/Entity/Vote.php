<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\VoteRepository;
use AppBundle\Event\Model\Talk;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: 'afup_sessions_vote_github')]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'session_id')]
    public int $sessionId = 0;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[ORM\Column(name: 'user')]
    public int $userId = 0;

    #[ORM\Column(nullable: true)]
    public ?string $comment = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(value: 0, message: 'Please set a note !')]
    #[ORM\Column]
    public int $vote = 0;

    #[ORM\Column(name: 'submitted_on', nullable: true)]
    public ?\DateTime $submittedOn = null;

    // Propriétés non mappées, renseignées uniquement en mémoire
    public ?Talk $talk = null;
}
