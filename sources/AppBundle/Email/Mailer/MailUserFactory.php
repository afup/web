<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer;

class MailUserFactory
{
    public static function afup(): MailUser
    {
        return new MailUser('bonjour@afup.org', 'AFUP');
    }

    public static function bureau(): MailUser
    {
        return new MailUser('bureau@afup.org', 'AFUP');
    }

    public static function sponsors(): MailUser
    {
        return new MailUser('sponsors@afup.org', 'AFUP sponsors');
    }

    public static function conferences(): MailUser
    {
        return new MailUser('conferences@afup.org', 'Pôle conférences');
    }

    public static function tresorier(): MailUser
    {
        return new MailUser('tresorier@afup.org');
    }
}
