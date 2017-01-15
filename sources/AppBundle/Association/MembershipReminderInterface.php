<?php


namespace AppBundle\Association;


interface MembershipReminderInterface
{
    public function sendReminder(NotifiableInterface $user);
}
