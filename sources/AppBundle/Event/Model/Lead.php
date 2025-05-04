<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Lead implements \JsonSerializable
{
    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $firstname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $lastname;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $company;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $phone;

    /**
     * @var string
     */
    #[Assert\NotBlank]
    private $language;

    /**
     * @var string
     */
    #[Assert\Url]
    private $website;

    private ?Event $event = null;

    /**
     * @var string|null
     */
    private $poste;

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname): self
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
     */
    public function setLastname($lastname): self
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
     */
    public function setEmail($email): self
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
     */
    public function setCompany($company): self
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
     */
    public function setPhone($phone): self
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
     */
    public function setLanguage($language): self
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
     */
    public function setWebsite($website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @return Event
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'website' => $this->website,
            'company' => $this->company,
            'language' => $this->language,
            'email' => $this->email,
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
    public function setPoste($poste): void
    {
        $this->poste = $poste;
    }
}
