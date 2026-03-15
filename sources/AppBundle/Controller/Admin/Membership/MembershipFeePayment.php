<?php

declare (strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MembershipFeePayment: int
{
    case Cash = 0;
    case Check = 1;
    case BankTransfert = 2;
    case Other = 3;
    case OnlinePayment = 4;

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Check => 'Chèques',
            self::BankTransfert => 'Virement',
            self::Other => 'Autre',
            self::OnlinePayment => 'En ligne',
        };
    }
}
