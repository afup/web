<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Invoices\Generator;

use AppBundle\Accounting\Invoices\Dto\InvoiceData;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;

class CompanyMemberInvoiceGenerator implements InvoiceGeneration
{
    public function generate(User|CompanyMember $member): InvoiceData
    {
        return new InvoiceData(
            $member->getCompanyName(),
            $member->getAddress(),
            $member->getZipcode(),
            $member->getCity(),
            $member->getCompanyName(),
        );
    }

    public function support(User|CompanyMember $user): bool
    {
        return $user instanceof CompanyMember;
    }
}
