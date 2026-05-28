<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Invoices\Generator;

use AppBundle\Accounting\Invoices\Dto\InvoiceData;
use AppBundle\Association\Model\CompanyMember;
use AppBundle\Association\Model\User;

interface InvoiceGeneration
{
    public function generate(User|CompanyMember $member): InvoiceData;

    public function support(User|CompanyMember $user): bool;
}
