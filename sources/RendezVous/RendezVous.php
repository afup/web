<?php

namespace App\RendezVous;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class RendezVous implements NotifyPropertyInterface
{
    use NotifyProperty;

    const COEF_COMING = 1.1;
    const COEF_PENDING = 1.3;
    /** @var int */
    private $id;
    /** @var string */
    private $title;
    /** @var string */
    private $pitch;
    /** @var string */
    private $theme;
    /** @var int */
    private $start;
    /** @var int */
    private $end;
    /** @var string */
    private $place;
    /** @var string */
    private $url;
    /** @var string */
    private $plan;
    /** @var string */
    private $address;
    /** @var int */
    private $capacity;
    /** @var int */
    private $officeId;
    /** @var bool */
    private $registration = false;
    /** @var string */
    private $externalUrl;

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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
    }

    public function getPitch()
    {
        return $this->pitch;
    }

    public function setPitch($pitch)
    {
        $this->propertyChanged('pitch', $this->pitch, $pitch);
        $this->pitch = $pitch;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->propertyChanged('theme', $this->theme, $theme);
        $this->theme = $theme;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->propertyChanged('start', $this->start, $start);
        $this->start = $start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->propertyChanged('end', $this->end, $end);
        $this->end = $end;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->propertyChanged('place', $this->place, $place);
        $this->place = $place;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->propertyChanged('url', $this->url, $url);
        $this->url = $url;
    }

    public function getPlan()
    {
        return $this->plan;
    }

    public function setPlan($plan)
    {
        $this->propertyChanged('plan', $this->plan, $plan);
        $this->plan = $plan;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->propertyChanged('address', $this->address, $address);
        $this->address = $address;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setCapacity($capacity)
    {
        $this->propertyChanged('capacity', $this->capacity, $capacity);
        $this->capacity = $capacity;
    }

    public function getOfficeId()
    {
        return $this->officeId;
    }

    public function setOfficeId($officeId)
    {
        $this->propertyChanged('officeId', $this->officeId, $officeId);
        $this->officeId = $officeId;
    }

    public function isRegistration()
    {
        return $this->registration;
    }

    /** Needed by Ting */
    public function getRegistration()
    {
        return $this->isRegistration();
    }

    public function setRegistration($registration)
    {
        $this->propertyChanged('registration', $this->registration, $registration);
        $this->registration = $registration;
    }

    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    public function setExternalUrl($externalUrl)
    {
        $this->propertyChanged('externalUrl', $this->externalUrl, $externalUrl);
        $this->externalUrl = $externalUrl;
    }
}
