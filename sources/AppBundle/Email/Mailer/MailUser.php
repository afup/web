<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

class MailUser
{
    const DEFAULT_SENDER_NAME = 'Bureau AFUP';
    const DEFAULT_SENDER_EMAIL = 'bureau@afup.org';

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
