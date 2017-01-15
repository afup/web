<?php


namespace AppBundle\Association\UserMembership;


use Afup\Site\Utils\Mail;
use AppBundle\Association\MembershipReminderInterface;
use AppBundle\Association\NotifiableInterface;

abstract class AbstractUserReminder implements MembershipReminderInterface
{
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    abstract protected function getText();
    abstract protected function getSubject();

    public function sendReminder(NotifiableInterface $user)
    {
        $this->mail->send('message-transactionnel-afup-org',
            ['email' => $user->getEmail()],
            [
                'content' => $this->getText(),
                'title' => $this->getSubject()
            ],
            ['subject' => $this->getSubject()]
        );
    }
}
