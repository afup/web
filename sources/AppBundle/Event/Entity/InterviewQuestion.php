<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'interview_question')]
class InterviewQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Interview::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(name: 'interview_id', nullable: false)]
    public Interview $interview;

    #[ORM\Column(nullable: false)]
    public int $position = 0;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $question;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $reponse;
}
