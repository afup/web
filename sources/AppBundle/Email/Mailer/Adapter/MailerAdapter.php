<?php

declare(strict_types=1);

namespace AppBundle\Email\Mailer\Adapter;

use AppBundle\Email\Mailer\Message;

interface MailerAdapter
{
    public function send(Message $message);
}
