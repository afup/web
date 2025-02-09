<?php

declare(strict_types=1);

namespace AppBundle\Mailchimp;

use AppBundle\Association\Event\UserDisabledEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventEventSubscriber implements EventSubscriberInterface
{
    private Mailchimp $mailchimp;

    private $membersList;

    public function __construct(Mailchimp $mailchimp, $membersList)
    {
        $this->mailchimp = $mailchimp;
        $this->membersList = $membersList;
    }

    public function onUserDisabled(UserDisabledEvent $userDisabledEvent): void
    {
        $this->mailchimp->unSubscribeAddress($this->membersList, $userDisabledEvent->getUser()->getEmail());
    }
    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return ['user.disabled' => 'onUserDisabled'];
    }
}
