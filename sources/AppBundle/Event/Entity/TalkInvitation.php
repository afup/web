<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\TalkInvitationRepository;
use AppBundle\Event\Enum\TalkInvitationState;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalkInvitationRepository::class)]
#[ORM\Table(name: 'afup_sessions_invitation')]
class TalkInvitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'talk_id', nullable: false)]
    public int $talkId;

    #[ORM\Column(nullable: false, enumType: TalkInvitationState::class)]
    public TalkInvitationState $state = TalkInvitationState::Pending;

    #[ORM\Column(name: 'submitted_on', nullable: false)]
    public \DateTime $submittedOn;

    #[ORM\Column(name: 'submitted_by', nullable: false)]
    public int $submittedBy;

    #[ORM\Column(length: 255, nullable: false)]
    public string $token;

    #[ORM\Column(length: 255, nullable: false)]
    public string $email;
}
