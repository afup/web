<?php

declare(strict_types=1);


namespace AppBundle\Association\UserMembership;

use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Email\Mailer\Mailer;

class UserReminderFactory
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly SubscriptionReminderLogRepository $subscriptionReminderLogRepository,
    ) {
    }

    /**
     * @param $class
     * @return MembershipReminderInterface
     */
    public function getReminder($class)
    {
        $instance = new $class($this->mailer, AFUP_COTISATION_PERSONNE_PHYSIQUE, $this->subscriptionReminderLogRepository);
        if (!$instance instanceof AbstractUserReminder) {
            throw new \RuntimeException(sprintf('The class %s is not an instance of AbstractUserReminder', $class));
        }
        return $instance;
    }
}
