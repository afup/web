<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Invoices\Generator;

use AppBundle\Accounting\Invoices\Dto\InvoiceData;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Model\User;

class CompanyMemberInvoiceGenerator implements InvoiceGeneration
{
    public function generate(User|CompanyMember $member): InvoiceData
    {
        if (!$member instanceof CompanyMember) {
            throw new \InvalidArgumentException('Expected a CompanyMember.');
        }

        return new InvoiceData(
            $member->companyName,
            $member->address,
            $member->zipCode,
            $member->city,
            $member->companyName,
        );
    }

    public function support(User|CompanyMember $user): bool
    {
        return $user instanceof CompanyMember;
    }
}
