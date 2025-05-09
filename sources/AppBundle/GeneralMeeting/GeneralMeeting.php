<?php

declare(strict_types=1);

namespace AppBundle\GeneralMeeting;

class GeneralMeeting
{
    /**
     * @param int $id
     * @param int $personnePhysiqueId
     * @param int $presence
     * @param int $personneAvecPouvoirId
     */
    public function __construct(
        private $id,
        private $personnePhysiqueId,
        private readonly \DateTimeInterface $date,
        private $presence,
        private $personneAvecPouvoirId,
        private readonly ?\DateTimeInterface $consultationDate = null,
        private readonly ?\DateTimeInterface $modificationDate = null,
    ) {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPersonnePhysiqueId()
    {
        return $this->personnePhysiqueId;
    }

    public function getDate(): \DateTimeInterface
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

    public function getConsultationDate(): ?\DateTimeInterface
    {
        return $this->consultationDate;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }
}
