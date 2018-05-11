<?php


namespace AppBundle\Association\UserMembership;

use Afup\Site\Utils\Mail;
use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\SubscriptionReminderLog;
use AppBundle\Association\NotifiableInterface;

abstract class AbstractUserReminder implements MembershipReminderInterface
{
    /**
     * @var Mail
     */
    private $mail;

    protected $membershipFee;

    private $subscriptionReminderLogRepository;

    /**
     * AbstractUserReminder constructor.
     *
     * @param Mail $mail
     * @param int $membershipFee
     * @param SubscriptionReminderLogRepository $subscriptionReminderLogRepository
     */
    public function __construct(Mail $mail, $membershipFee, SubscriptionReminderLogRepository $subscriptionReminderLogRepository)
    {
        $this->mail = $mail;
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

        $status = $this->mail->send('message-transactionnel-afup-org',
            ['email' => $user->getEmail()],
            [
                'content' => $this->getText(),
                'title' => $this->getSubject()
            ],
            ['subject' => $this->getSubject()],
            false,
            null,
            null,
            false
        );
        $log->setMailSent($status);
        $this->subscriptionReminderLogRepository->save($log);
    }
}
