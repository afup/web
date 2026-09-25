<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\SpeakerSuggestionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpeakerSuggestionRepository::class)]
#[ORM\Table(name: 'afup_speaker_suggestion')]
class SpeakerSuggestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(name: 'event_id', nullable: false)]
    public int $eventId;

    #[ORM\Column(name: 'suggester_email', length: 255, nullable: false)]
    public string $suggesterEmail;

    #[ORM\Column(name: 'suggester_name', length: 255, nullable: false)]
    public string $suggesterName;

    #[ORM\Column(name: 'speaker_name', length: 255, nullable: false)]
    public string $speakerName;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $comment = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $createdAt;
}
