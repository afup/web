<?php

declare(strict_types=1);

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyMemberInvitation implements NotifyPropertyInterface
{
    use NotifyProperty;

    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_CANCELLED = 2;

    private int $id;

    private int $companyId;

    #[Assert\Email]
    private string $email;

    private string $token;

    private bool $manager = false;

    private \DateTime $submittedOn;

    /**
     * @var self::STATUS_*
     */
    private int $status = self::STATUS_PENDING;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->propertyChanged('id', $this->id ?? null, $id);
        $this->id = $id;
        return $this;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->propertyChanged('companyId', $this->companyId ?? null, $companyId);
        $this->companyId = $companyId;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->propertyChanged('email', $this->email ?? null, $email);
        $this->email = $email;
        return $this;
    }

    public function getManager(): bool
    {
        return $this->manager;
    }

    public function setManager(bool $manager): self
    {
        $this->propertyChanged('manager', $this->manager ?? null, $manager);
        $this->manager = $manager;
        return $this;
    }

    public function getSubmittedOn(): \DateTime
    {
        return $this->submittedOn;
    }

    public function setSubmittedOn(\DateTime $submittedOn): self
    {
        $this->propertyChanged('submittedOn', $this->submittedOn ?? null, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return self::STATUS_*
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param self::STATUS_* $status
     */
    public function setStatus(int $status): self
    {
        $this->propertyChanged('status', $this->status ?? null, $status);
        $this->status = $status;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->propertyChanged('token', $this->token ?? null, $token);
        $this->token = $token;
        return $this;
    }
}
