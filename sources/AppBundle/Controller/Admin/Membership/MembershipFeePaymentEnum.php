<?php

declare (strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MembershipFeePaymentEnum: int
{
    case CASH = 0;
    case CHECK = 1;
    case BANK_TRANSFERT = 2;
    case ONLINE_PAYMENT = 3;
    case OTHER = 4;

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Espèces',
            self::CHECK => 'Chèques',
            self::BANK_TRANSFERT => 'Virement',
            self::ONLINE_PAYMENT => 'En ligne',
            self::OTHER => 'Autre',
        };
    }
}
