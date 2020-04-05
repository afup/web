<?php

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\SubscriptionReminderLog;
use AppBundle\Association\NotifiableInterface;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;

abstract class AbstractUserReminder implements MembershipReminderInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    protected $membershipFee;

    private $subscriptionReminderLogRepository;

    /**
     * AbstractUserReminder constructor.
     *
     * @param Mailer                            $mailer
     * @param int                               $membershipFee
     * @param SubscriptionReminderLogRepository $subscriptionReminderLogRepository
     */
    public function __construct(Mailer $mailer, $membershipFee, SubscriptionReminderLogRepository $subscriptionReminderLogRepository)
    {
        $this->mailer = $mailer;
        $this->membershipFee = $membershipFee;
        $this->subscriptionReminderLogRepository = $subscriptionReminderLogRepository;
    }

    abstract protected function getText();
    abstract protected function getSubject();
    abstract protected function getKey();

    public function sendReminder(NotifiableInterface $user)
    {
        $log = new SubscriptionReminderLog();
        $log
            ->setEmail($user->getEmail())
            ->setUserId($user->getId())
            ->setReminderDate(new \DateTime())
            ->setReminderKey($this->getKey())
            ->setUserType(UserRepository::USER_TYPE_PHYSICAL)
        ;

        $status = $this->mailer->sendTransactional(new Message(
            $this->getSubject(),
            MailUserFactory::sponsors(),
            new MailUser($user->getEmail())
        ), $this->getText(), MailUserFactory::bureau()->getEmail());
        $log->setMailSent($status);
        $this->subscriptionReminderLogRepository->save($log);
    }
}
