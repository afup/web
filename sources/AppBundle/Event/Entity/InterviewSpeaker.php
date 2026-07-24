<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interview_speaker')]
class InterviewSpeaker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Interview::class, inversedBy: 'speakers')]
    #[ORM\JoinColumn(name: 'interview_id', nullable: false)]
    public Interview $interview;

    #[ORM\Column(name: 'speaker_id', nullable: false)]
    public int $idSpeaker;
}
