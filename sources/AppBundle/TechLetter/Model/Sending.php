<?php

declare(strict_types=1);

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

    private ?\DateTime $sendingDate = null;

    /**
     * @var bool
     */
    private $sentToMailchimp = false;

    /**
     * @var string
     */
    private $techletter;

    /**
     * @var string
     */
    private $archiveUrl;

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
     * @return \DateTime
     */
    public function getSendingDate(): ?\DateTime
    {
        return $this->sendingDate;
    }

    public function setSendingDate(\DateTime $sendingDate = null): self
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
     */
    public function setSentToMailchimp($sentToMailchimp): self
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
     */
    public function setTechletter($techletter): self
    {
        $this->propertyChanged('techletter', $this->techletter, $techletter);
        $this->techletter = $techletter;
        return $this;
    }

    /**
     * @return string
     */
    public function getArchiveUrl()
    {
        return $this->archiveUrl;
    }

    /**
     * @param string $archiveUrl
     *
     * @return $this
     */
    public function setArchiveUrl($archiveUrl): self
    {
        $this->propertyChanged('archiveUrl', $this->archiveUrl, $archiveUrl);
        $this->archiveUrl = $archiveUrl;

        return $this;
    }
}
