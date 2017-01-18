<?php


namespace AppBundle\Association\UserMembership;


use Afup\Site\Utils\Mail;
use AppBundle\Association\MembershipReminderInterface;

class UserReminderFactory
{
    /**
     * @var Mail
     */
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param $class
     * @return MembershipReminderInterface
     */
    public function getReminder($class)
    {
        $instance = new $class($this->mail, AFUP_COTISATION_PERSONNE_PHYSIQUE);
        if (!$instance instanceof AbstractUserReminder) {
            throw new \RuntimeException(sprintf('The class %s is not an instance of AbstractUserReminder', $class));
        }
        return $instance;
    }
}
