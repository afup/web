<?php

namespace AppBundle\Event\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Lead implements \JsonSerializable
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $company;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $phone;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $language;

    /**
     * @var string
     * @Assert\Url()
     */
    private $website;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var string|null
     */
    private $poste = null;

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Lead
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Lead
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Lead
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return Lead
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return Lead
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return Lead
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return Lead
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return Lead
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'website' => $this->website,
            'company' => $this->company,
            'language' => $this->language,
            'email' => $this->email
        ];
    }

    /**
     * @return string|null
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * @param string|null $poste
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;
    }
}
