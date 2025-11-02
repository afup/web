<?php

declare (strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MembershipFeePayment: int
{
    case Cash = 0;
    case Check = 1;
    case BankTransfer = 2;
    case OnlinePayment = 3;
    case Other = 4;

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Check => 'Chèques',
            self::BankTransfer => 'Virement',
            self::OnlinePayment => 'En ligne',
            self::Other => 'Autre',
        };
    }
}
