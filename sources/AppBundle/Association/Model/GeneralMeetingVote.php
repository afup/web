<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class GeneralMeetingVote implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $questionId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     *
     * @return $this
     */
    public function setQuestionId($questionId)
    {
        $this->propertyChanged('questionId', $this->questionId, $questionId);
        $this->questionId = $questionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->propertyChanged('userId', $this->userId, $userId);
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->propertyChanged('weight', $this->weight, $weight);
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->propertyChanged('value', $this->value, $value);
        $this->value = $value;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->propertyChanged('createdAt', $this->createdAt, $createdAt);
        $this->createdAt = $createdAt;

        return $this;
    }
}
