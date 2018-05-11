<?php

namespace AppBundle\TechLetter\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Sending implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $sendingDate;

    /**
     * @var bool
     */
    private $sentToMailchimp = false;

    /**
     * @var string
     */
    private $techletter;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Sending
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendingDate()
    {
        return $this->sendingDate;
    }

    /**
     * @param \DateTime $sendingDate
     * @return Sending
     */
    public function setSendingDate(\DateTime $sendingDate = null)
    {
        $this->propertyChanged('sendingDate', $this->sendingDate, $sendingDate);
        $this->sendingDate = $sendingDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSentToMailchimp()
    {
        return $this->sentToMailchimp;
    }

    /**
     * @param bool $sentToMailchimp
     * @return Sending
     */
    public function setSentToMailchimp($sentToMailchimp)
    {
        $this->propertyChanged('sentToMailchimp', $this->sentToMailchimp, $sentToMailchimp);
        $this->sentToMailchimp = $sentToMailchimp;
        return $this;
    }

    /**
     * @return string
     */
    public function getTechletter()
    {
        return $this->techletter;
    }

    /**
     * @param string $techletter
     * @return Sending
     */
    public function setTechletter($techletter)
    {
        $this->propertyChanged('techletter', $this->techletter, $techletter);
        $this->techletter = $techletter;
        return $this;
    }
}
