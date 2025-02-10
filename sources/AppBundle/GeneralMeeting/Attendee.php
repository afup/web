<?php

declare(strict_types=1);

namespace AppBundle\GeneralMeeting;

use AppBundle\Association\Model\GeneralMeetingResponse;
use DateTimeImmutable;

class Attendee
{
    /** @var int */
    private $id;
    /** @var string */
    private $email;
    /** @var string */
    private $login;
    /** @var string */
    private $lastname;
    /** @var string */
    private $firstname;
    /** @var string */
    private $nearestOffice;
    /** @var DateTimeImmutable|null */
    private $consultationDate;
    /** @var int */
    private $presence;
    /** @var int|null */
    private $powerId;
    /** @var string|null */
    private $powerLastname;
    /** @var string|null */
    private $powerFirstname;

    /**
     * @param int                    $id
     * @param string                 $email
     * @param string                 $login
     * @param string                 $lastname
     * @param string                 $firstname
     * @param string                 $nearestOffice
     * @param DateTimeImmutable|null $consultationDate
     * @param int                    $presence
     * @param int|null               $powerId
     * @param string|null            $powerLastname
     * @param string|null            $powerFirstname
     */
    public function __construct(
        $id,
        $email,
        $login,
        $lastname,
        $firstname,
        $nearestOffice,
        $consultationDate,
        $presence,
        $powerId,
        $powerLastname,
        $powerFirstname
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->login = $login;
        $this->lastname = $lastname;
        $this->firstname = $firstname;
        $this->nearestOffice = $nearestOffice;
        $this->consultationDate = $consultationDate;
        $this->presence = $presence;
        $this->powerId = $powerId;
        $this->powerLastname = $powerLastname;
        $this->powerFirstname = $powerFirstname;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getNearestOffice()
    {
        return $this->nearestOffice;
    }

    public function getConsultationDate()
    {
        return $this->consultationDate;
    }

    public function getPresence()
    {
        return $this->presence;
    }

    public function isPresent(): bool
    {
        return $this->presence === GeneralMeetingResponse::STATUS_PRESENT;
    }

    public function isAbsent(): bool
    {
        return $this->presence === GeneralMeetingResponse::STATUS_NON_PRESENT;
    }

    public function getPowerId()
    {
        return $this->powerId;
    }

    public function getPowerLastname()
    {
        return $this->powerLastname;
    }

    public function getPowerFirstname()
    {
        return $this->powerFirstname;
    }

    public function getHash(): string
    {
        return md5($this->id . '_' . $this->email . '_' . $this->login);
    }
}
