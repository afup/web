<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Invoices\Generator;

use AppBundle\Accounting\Invoices\Dto\InvoiceData;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Model\User;

class MemberInvoiceGenerator implements InvoiceGeneration
{
    public function generate(User|CompanyMember $member): InvoiceData
    {
        if (!$member instanceof User) {
            throw new \InvalidArgumentException('Expected a User.');
        }

        return new InvoiceData(
            $member->getFirstName() . ' ' . $member->getLastName(),
            $member->getAddress(),
            $member->getZipcode(),
            $member->getCity(),
            $member->getLastName(),
        );
    }

    public function support(User|CompanyMember $user): bool
    {
        return $user instanceof User;
    }
}
