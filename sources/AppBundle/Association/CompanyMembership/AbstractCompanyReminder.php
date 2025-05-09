<?php

declare(strict_types=1);

namespace AppBundle\Association\CompanyMembership;

use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\Model\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\SubscriptionReminderLog;
use AppBundle\Association\NotifiableInterface;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;

abstract class AbstractCompanyReminder implements MembershipReminderInterface
{
    public function __construct(
        private readonly Mailer $mailer,
        protected int $membershipFee,
        protected int $membersPerFee,
        private readonly SubscriptionReminderLogRepository $subscriptionReminderLogRepository,
    ) {
    }

    abstract protected function getText();
    abstract protected function getSubject();
    abstract protected function getKey();

    public function sendReminder(NotifiableInterface $user): void
    {
        $log = new SubscriptionReminderLog();
        $log
            ->setEmail($user->getEmail())
            ->setUserId($user->getId())
            ->setReminderDate(new \DateTime())
            ->setReminderKey($this->getKey())
            ->setUserType(UserRepository::USER_TYPE_COMPANY)
        ;

        $message = new Message($this->getSubject(), MailUserFactory::sponsors(), new MailUser($user->getEmail()));

        $message->addAttachment(new Attachment(
            __DIR__ . '/../_data/membership-renouvellement-cotisation.pdf',
            'membership-renouvellement-cotisation.pdf',
            'base64',
            'application/pdf'
        ));

        $status = $this->mailer->sendTransactional($message, $this->getText());
        $log->setMailSent($status);
        $this->subscriptionReminderLogRepository->save($log);
    }
}
