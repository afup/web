<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TechletterUnsubscription implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $unsubscriptionDate;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var string
     */
    private $mailchimpId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUnSubscriptionDate()
    {
        return $this->unsubscriptionDate;
    }

    /**
     * @param \DateTime $unsubscriptionDate
     *
     * @return $this
     */
    public function setUnsubscriptionDate(\DateTime $unsubscriptionDate)
    {
        $this->propertyChanged('unsubscriptionDate', $this->unsubscriptionDate, $unsubscriptionDate);
        $this->unsubscriptionDate = $unsubscriptionDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     *
     * @return $this
     */
    public function setReason($reason)
    {
        $this->propertyChanged('reason', $this->reason, $reason);
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailchimpId()
    {
        return $this->mailchimpId;
    }

    /**
     * @param string $mailchimpId
     *
     * @return $this
     */
    public function setMailchimpId($mailchimpId)
    {
        $this->propertyChanged('mailchimpId', $this->mailchimpId, $mailchimpId);
        $this->mailchimpId = $mailchimpId;

        return $this;
    }
}
