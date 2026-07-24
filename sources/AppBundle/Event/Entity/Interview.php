<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\InterviewRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewRepository::class)]
#[ORM\Table(name: 'interview')]
class Interview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(name: 'event_id', nullable: false)]
    public int $eventId;

    #[ORM\Column(name: 'date_publication', type: 'datetime_immutable', nullable: false)]
    public DateTimeImmutable $datePublication;

    #[ORM\Column(name: 'wordpress_post_id', nullable: true)]
    public ?int $wordpressPostId = null;

    /** @var Collection<int, Speaker> */
    #[ORM\JoinTable(name: 'interview_speaker')]
    #[ORM\JoinColumn(name: 'interview_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'speaker_id', referencedColumnName: 'conferencier_id', unique: true)]
    #[ORM\ManyToMany(targetEntity: Speaker::class)]
    public Collection $speakers;

    /** @var Collection<int, InterviewQuestion> */
    #[ORM\OneToMany(
        targetEntity: InterviewQuestion::class,
        mappedBy: 'interview',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    public Collection $questions;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    /**
     * @return array<int>
     */
    public function getSpeakerIds(): array
    {
        return $this->speakers->map(static fn(Speaker $speaker): int => $speaker->id)->toArray();
    }

    public function addQuestion(InterviewQuestion $question): void
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->interview = $this;
        }
    }

    public function removeQuestion(InterviewQuestion $question): void
    {
        $this->questions->removeElement($question);
    }
}
