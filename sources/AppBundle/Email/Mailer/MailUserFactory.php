<?php

namespace AppBundle\Email\Mailer;

class MailUserFactory
{
    public static function afup()
    {
        return new MailUser('bonjour@afup.org', 'AFUP');
    }

    public static function bureau()
    {
        return new MailUser('bureau@afup.org', 'AFUP');
    }

    public static function sponsors()
    {
        return new MailUser('sponsors@afup.org', 'AFUP sponsors');
    }

    public static function conferences()
    {
        return new MailUser('conferences@afup.org', 'Pôle conférences');
    }

    public static function tresorier()
    {
        return new MailUser('tresorier@afup.org');
    }
}
