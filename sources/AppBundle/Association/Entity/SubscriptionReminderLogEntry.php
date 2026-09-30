<?php

declare(strict_types=1);

namespace AppBundle\Association\Entity;

use DateTimeImmutable;

final readonly class SubscriptionReminderLogEntry
{
    public function __construct(
        public int $id,
        public ?string $nom,
        public ?string $prenom,
        public ?int $appId,
        public ?int $apmId,
        public ?string $raisonSociale,
        public string $reminderKey,
        public DateTimeImmutable $reminderDate,
        public bool $mailSent,
    ) {}
}
