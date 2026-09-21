<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use AppBundle\Association\MemberType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'afup_subscription_reminder_log')]
class SubscriptionReminderLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    public int $userId;

    #[ORM\Column(enumType: MemberType::class)]
    public MemberType $userType;

    #[ORM\Column(length: 255)]
    public string $email;

    #[ORM\Column(length: 30)]
    public string $reminderKey;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $reminderDate;

    #[ORM\Column]
    public bool $mailSent = false;
}
