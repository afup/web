<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class TalkToSpeaker implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $talkId;

    /**
     * @var int
     */
    private $speakerId;

    /**
     * @return int
     */
    public function getTalkId()
    {
        return $this->talkId;
    }

    /**
     * @param int $talkId
     */
    public function setTalkId($talkId): self
    {
        $this->propertyChanged('talkId', $this->talkId, $talkId);
        $this->talkId = $talkId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpeakerId()
    {
        return $this->speakerId;
    }

    /**
     * @param int $speakerId
     */
    public function setSpeakerId($speakerId): self
    {
        $this->propertyChanged('speakerId', $this->speakerId, $speakerId);
        $this->speakerId = $speakerId;
        return $this;
    }
}
