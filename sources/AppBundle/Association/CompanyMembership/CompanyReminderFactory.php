<?php

declare(strict_types=1);


namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Email\Mailer\Mailer;

class CompanyReminderFactory
{
    private Mailer $mailer;

    private SubscriptionReminderLogRepository $subscriptionReminderLogRepository;

    public function __construct(Mailer $mailer, SubscriptionReminderLogRepository $subscriptionReminderLogRepository)
    {
        $this->mailer = $mailer;
        $this->subscriptionReminderLogRepository = $subscriptionReminderLogRepository;
    }

    /**
     * @param $class
     * @return MembershipReminderInterface
     */
    public function getReminder($class)
    {
        $instance = new $class(
            $this->mailer,
            AFUP_COTISATION_PERSONNE_MORALE,
            AFUP_PERSONNE_MORALE_SEUIL,
            $this->subscriptionReminderLogRepository
        );
        if (!$instance instanceof AbstractCompanyReminder) {
            throw new \RuntimeException(sprintf('The class %s is not an instance of AbstractCompany', $class));
        }
        return $instance;
    }
}
