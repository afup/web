<?php


namespace AppBundle\Association\UserMembership;


use Afup\Site\Utils\Mail;
use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;

class UserReminderFactory
{
    /**
     * @var Mail
     */
    private $mail;

    /**
     * @var SubscriptionReminderLogRepository
     */
    private $subscriptionReminderLogRepository;

    public function __construct(Mail $mail, SubscriptionReminderLogRepository $subscriptionReminderLogRepository)
    {
        $this->mail = $mail;
        $this->subscriptionReminderLogRepository = $subscriptionReminderLogRepository;
    }

    /**
     * @param $class
     * @return MembershipReminderInterface
     */
    public function getReminder($class)
    {
        $instance = new $class($this->mail, AFUP_COTISATION_PERSONNE_PHYSIQUE, $this->subscriptionReminderLogRepository);
        if (!$instance instanceof AbstractUserReminder) {
            throw new \RuntimeException(sprintf('The class %s is not an instance of AbstractUserReminder', $class));
        }
        return $instance;
    }
}
