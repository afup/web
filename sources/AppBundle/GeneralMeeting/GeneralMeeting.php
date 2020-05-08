<?php

namespace AppBundle\GeneralMeeting;

use DateTimeInterface;

class GeneralMeeting
{
    /** @var int */
    private $id;
    /** @var int */
    private $personnePhysiqueId;
    /** @var DateTimeInterface */
    private $date;
    /** @var int */
    private $presence;
    /** @var int */
    private $personneAvecPouvoirId;
    /** @var DateTimeInterface|null */
    private $consultationDate;
    /** @var DateTimeInterface|null */
    private $modificationDate;

    /**
     * @param int $id
     * @param int $personnePhysiqueId
     * @param int $presence
     * @param int $personneAvecPouvoirId
     */
    public function __construct(
        $id,
        $personnePhysiqueId,
        DateTimeInterface $date,
        $presence,
        $personneAvecPouvoirId,
        DateTimeInterface $consultationDate = null,
        DateTimeInterface $modificationDate = null
    ) {
        $this->id = $id;
        $this->personnePhysiqueId = $personnePhysiqueId;
        $this->date = $date;
        $this->presence = $presence;
        $this->personneAvecPouvoirId = $personneAvecPouvoirId;
        $this->consultationDate = $consultationDate;
        $this->modificationDate = $modificationDate;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPersonnePhysiqueId()
    {
        return $this->personnePhysiqueId;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getPresence()
    {
        return $this->presence;
    }

    public function getPersonneAvecPouvoirId()
    {
        return $this->personneAvecPouvoirId;
    }

    public function getConsultationDate()
    {
        return $this->consultationDate;
    }

    public function getModificationDate()
    {
        return $this->modificationDate;
    }
}
