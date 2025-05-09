<?php

declare(strict_types=1);


namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyMemberInvitation implements NotifyPropertyInterface
{
    use NotifyProperty;

    const STATUS_PENDING = 0;
    const STATUS_ACCEPTED = 1;
    const STATUS_CANCELLED = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $companyId;

    /**
     * @var string
     */
    #[Assert\Email]
    private $email;

    /**
     * @var string
     */
    private $token;

    /**
     * @var bool
     */
    private $manager = false;

    private ?\DateTime $submittedOn = null;

    /**
     * @var int
     */
    private $status = self::STATUS_PENDING;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     */
    public function setCompanyId($companyId): self
    {
        $this->propertyChanged('companyId', $this->companyId, $companyId);
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): self
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param boolean $manager
     */
    public function setManager($manager): self
    {
        $this->propertyChanged('manager', $this->manager, $manager);
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn(): ?\DateTime
    {
        return $this->submittedOn;
    }

    public function setSubmittedOn(\DateTime $submittedOn): self
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status): self
    {
        $this->propertyChanged('status', $this->status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token): self
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;
        return $this;
    }
}
