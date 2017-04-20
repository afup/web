<?php

namespace AppBundle\Mailchimp;

use AppBundle\Association\Event\UserDisabledEvent;

class EventListener
{
    /**
     * @var Mailchimp
     */
    private $mailchimp;

    private $membersList;

    public function __construct(Mailchimp $mailchimp, $membersList)
    {
        $this->mailchimp = $mailchimp;
        $this->membersList = $membersList;
    }

    public function onUserDisabled(UserDisabledEvent $userDisabledEvent)
    {
        $this->mailchimp->unSubscribeAddress($this->membersList, $userDisabledEvent->getUser()->getEmail());
    }
}
