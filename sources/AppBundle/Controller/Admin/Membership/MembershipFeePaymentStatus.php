<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MembershipFeePaymentStatus: int
{
    case Error = 0;
    case Paid = 1;
    case Cancelled = 2;
    case Rejected = 3;
}
