<?php

declare(strict_types=1);

namespace AppBundle\AssembleeGenerale\Dto;

use AppBundle\AssembleeGenerale\Enum\PresenceEtat;
use DateTimeImmutable;

class Attendee
{
    public function __construct(
        private int $id,
        private string $email,
        private string $login,
        private string $lastname,
        private string $firstname,
        private string $nearestOffice,
        private ?DateTimeImmutable $consultationDate,
        private int $presence,
        private ?int $powerId,
        private ?string $powerLastname,
        private ?string $powerFirstname,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getNearestOffice(): string
    {
        return $this->nearestOffice;
    }

    public function getConsultationDate(): ?DateTimeImmutable
    {
        return $this->consultationDate;
    }

    public function getPresence(): int
    {
        return $this->presence;
    }

    public function isPresent(): bool
    {
        return $this->presence === PresenceEtat::Present->value;
    }

    public function isAbsent(): bool
    {
        return $this->presence === PresenceEtat::NonPresent->value;
    }

    public function getPowerId(): ?int
    {
        return $this->powerId;
    }

    public function getPowerLastname(): ?string
    {
        return $this->powerLastname;
    }

    public function getPowerFirstname(): ?string
    {
        return $this->powerFirstname;
    }

    public function getHash(): string
    {
        return md5($this->id . '_' . $this->email . '_' . $this->login);
    }
}
