<?php

namespace AppBundle\Offices;

use AppBundle\Association\Model\User;
use AppBundle\Event\Model\Invoice;

class NullOfficeFinder extends OfficeFinder
{
    public function findOffice(Invoice $invoice, User $user = null)
    {
        return null;
    }
}
