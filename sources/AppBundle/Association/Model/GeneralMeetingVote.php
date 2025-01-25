<?php

namespace AppBundle\Association\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class GeneralMeetingVote implements NotifyPropertyInterface
{
    use NotifyProperty;

    const VALUE_YES = 'oui';
    const VALUE_NO = 'non';
    const VALUE_ABSTENTION = 'abstention';

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
     * @return string|null
     */
    public function getValueLabel()
    {
        $valuesLabels = self::getVoteLabelsByValue();
        $value = $this->getValue();

        return $valuesLabels[$value] ?? null;
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

    public static function isValueAllowed($value)
    {
        return in_array($value, self::getAllValues());
    }

    private static function getAllValues()
    {
        return array_keys(self::getVoteLabelsByValue());
    }

    public static function getVoteLabelsByValue()
    {
        return [
            self::VALUE_YES => 'Oui',
            self::VALUE_NO => 'Non',
            self::VALUE_ABSTENTION => 'Abstention',
        ];
    }
}
