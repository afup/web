<?php

declare(strict_types=1);


namespace AppBundle\Association;

interface MembershipReminderInterface
{
    public function sendReminder(NotifiableInterface $user);
}
