<?php

namespace AppBundle\Email\Mailer\Adapter;

use AppBundle\Email\Mailer\Message;

interface MailerAdapter
{
    public function send(Message $message);
}
