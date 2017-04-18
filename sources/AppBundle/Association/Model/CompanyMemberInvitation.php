<?php


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
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     */
    private $token;

    /**
     * @var bool
     * @Assert\
     */
    private $manager = false;

    /**
     * @var \DateTime
     */
    private $submittedOn;

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
     * @return CompanyMemberInvitation
     */
    public function setId($id)
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
     * @return CompanyMemberInvitation
     */
    public function setCompanyId($companyId)
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
     * @return CompanyMemberInvitation
     */
    public function setEmail($email)
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
     * @return CompanyMemberInvitation
     */
    public function setManager($manager)
    {
        $this->propertyChanged('manager', $this->manager, $manager);
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * @param \DateTime $submittedOn
     * @return CompanyMemberInvitation
     */
    public function setSubmittedOn(\DateTime $submittedOn)
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
     * @return CompanyMemberInvitation
     */
    public function setStatus($status)
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
     * @return CompanyMemberInvitation
     */
    public function setToken($token)
    {
        $this->propertyChanged('token', $this->token, $token);
        $this->token = $token;
        return $this;
    }
}
