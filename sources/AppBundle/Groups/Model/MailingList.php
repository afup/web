<?php

namespace AppBundle\Groups\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class MailingList implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $membersOnly;

    /**
     * @var boolean
     */
    private $autoRegistration;

    /**
     * @var string
     */
    private $category;

    private $categoryLabels = [
        'office' => 'Toutes les mailing lists de nos antennes',
        'member' => 'Mailing lists réservées aux membres'
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return MailingList
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
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
     * @return MailingList
     */
    public function setEmail($email)
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return MailingList
     */
    public function setName($name)
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return MailingList
     */
    public function setDescription($description)
    {
        $this->propertyChanged('description', $this->description, $description);
        $this->description = $description;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMembersOnly()
    {
        return $this->membersOnly;
    }

    /**
     * @param boolean $membersOnly
     * @return MailingList
     */
    public function setMembersOnly($membersOnly)
    {
        $membersOnly = (boolean)$membersOnly;
        $this->propertyChanged('membersOnly', $this->membersOnly, $membersOnly);
        $this->membersOnly = $membersOnly;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return MailingList
     */
    public function setCategory($category)
    {
        $this->propertyChanged('category', $this->category, $category);
        $this->category = $category;
        return $this;
    }

    public function getCategoryLabel()
    {
        return $this->categoryLabels[$this->category];
    }

    /**
     * @return bool
     */
    public function getAutoRegistration()
    {
        return $this->autoRegistration;
    }

    /**
     * @param bool $autoRegistration
     * @return MailingList
     */
    public function setAutoRegistration($autoRegistration)
    {
        $autoRegistration = (bool)$autoRegistration;
        $this->propertyChanged('autoRegistration', $this->autoRegistration, $autoRegistration);
        $this->autoRegistration = $autoRegistration;
        return $this;
    }
}
