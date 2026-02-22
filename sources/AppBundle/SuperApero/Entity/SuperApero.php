<?php

declare(strict_types=1);

namespace AppBundle\SuperApero\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\Clock;

#[ORM\Entity]
#[ORM\Table(name: 'super_apero')]
class SuperApero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'date_immutable', nullable: false)]
    public DateTimeImmutable $date;

    /** @var Collection<string, SuperAperoMeetup>&iterable<string, SuperAperoMeetup> */
    #[ORM\OneToMany(targetEntity: SuperAperoMeetup::class, mappedBy: 'superApero', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'antenne')]
    public Collection $meetups;

    public function __construct()
    {
        $this->meetups = new ArrayCollection();
    }

    public function annee(): int
    {
        return (int) $this->date->format('Y');
    }

    public function addMeetup(SuperAperoMeetup $meetup): void
    {
        if (!$this->meetups->contains($meetup)) {
            $this->meetups->add($meetup);
            $meetup->superApero = $this;
        }
    }

    public function removeMeetup(SuperAperoMeetup $meetup): void
    {
        $this->meetups->removeElement($meetup);
    }

    public function isActive(): bool
    {
        $now = Clock::get()->now();

        return $this->annee() === (int) $now->format('Y')
            && $this->date >= new DateTimeImmutable($now->format('Y-m-d'))
            && !$this->meetups->isEmpty();
    }
}
