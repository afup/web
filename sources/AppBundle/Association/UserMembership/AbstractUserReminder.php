<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\MemberType;
use AppBundle\Association\Entity\Repository\SubscriptionReminderLogRepository;
use AppBundle\Association\Entity\SubscriptionReminderLog;
use AppBundle\Association\NotifiableInterface;
use AppBundle\Email\Mailer\Attachment;
use AppBundle\Email\Mailer\Mailer;
use AppBundle\Email\Mailer\MailUser;
use AppBundle\Email\Mailer\MailUserFactory;
use AppBundle\Email\Mailer\Message;

abstract class AbstractUserReminder implements MembershipReminderInterface
{
    public function __construct(
        private readonly Mailer $mailer,
        protected int $membershipFee,
        private readonly SubscriptionReminderLogRepository $subscriptionReminderLogRepository,
    ) {}

    abstract protected function getText(): string;
    abstract protected function getSubject(): string;
    abstract protected function getKey(): string;

    public function sendReminder(NotifiableInterface $user): void
    {
        $log = new SubscriptionReminderLog();
        $log->email = (string) $user->getEmail();
        $log->userId = (int) $user->getId();
        $log->reminderDate = new \DateTimeImmutable();
        $log->reminderKey = $this->getKey();
        $log->userType = MemberType::MemberPhysical;

        $message = new Message(
            $this->getSubject(),
            MailUserFactory::bureau(),
            new MailUser($user->getEmail()),
        );

        $message->addAttachment(new Attachment(
            __DIR__ . '/../_data/membership-renouvellement-cotisation.pdf',
            'membership-renouvellement-cotisation.pdf',
            'base64',
            'application/pdf',
        ));

        $status = $this->mailer->sendTransactional($message, $this->getText(), MailUserFactory::bureau()->getEmail());
        $log->mailSent = $status;
        $this->subscriptionReminderLogRepository->save($log);
    }
}
