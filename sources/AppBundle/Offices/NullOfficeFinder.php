<?php

declare(strict_types=1);

namespace AppBundle\Offices;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Invoice;

class NullOfficeFinder extends OfficeFinder
{
    #[\Override]
    public function findOffice(Invoice $invoice, ?User $user = null): ?string
    {
        return null;
    }
}
