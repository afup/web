<?php

namespace AppBundle\Email\Mailer;

class MailUser
{
    /** @var string */
    private $email;
    /** @var string */
    private $name;

    /**
     * @param string $email
     * @param string $name
     */
    public function __construct($email, $name = '')
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
