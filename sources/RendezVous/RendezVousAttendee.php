<?php

namespace App\RendezVous;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class RendezVousAttendee implements NotifyPropertyInterface
{
    use NotifyProperty;

    const REFUSED = 0;
    const COMING = 1;
    const PENDING = 2;
    const CONFIRMED = 1;
    const DECLINED = -1;
    const COEF_COMING = 1.1;
    const COEF_PENDING = 1.3;
    /** @var int */
    private $id;
    /** @var int */
    private $rendezVousId;
    /** @var string */
    private $lastname;
    /** @var string */
    private $firstname;
    /** @var string */
    private $company;
    /** @var string */
    private $email;
    /** @var string */
    private $phone;
    /** @var int */
    private $presence;
    /** @var int */
    private $confirmed;
    /** @var int */
    private $creation;

    /** @return int */
    public function getId()
    {
        return $this->id;
    }

    /** @param int $id */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getRendezVousId()
    {
        return $this->rendezVousId;
    }

    public function setRendezVousId($rendezVousId)
    {
        $this->propertyChanged('rendezVousId', $this->rendezVousId, $rendezVousId);
        $this->rendezVousId = $rendezVousId;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->propertyChanged('phone', $this->phone, $phone);
        $this->phone = $phone;
    }

    public function getPresence()
    {
        return $this->presence;
    }

    public function setPresence($presence)
    {
        $this->propertyChanged('presence', $this->presence, $presence);
        $this->presence = $presence;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($confirmed)
    {
        $this->propertyChanged('confirmed', $this->confirmed, $confirmed);
        $this->confirmed = $confirmed;
    }

    public function getCreation()
    {
        return $this->creation;
    }

    public function setCreation($creation)
    {
        $this->propertyChanged('creation', $this->creation, $creation);
        $this->creation = $creation;
    }
}
