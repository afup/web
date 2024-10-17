<?php

declare(strict_types=1);

namespace AppBundle\Association\UserMembership;

class Statistics
{
    /** @var int */
    public $usersCount = 0;
    /** @var int */
    public $usersCountWithoutCompanies = 0;
    /** @var int */
    public $companiesCountWithLinkedUsers = 0;
    /** @var int */
    public $companiesCount = 0;
    /** @var int */
    public $usersCountWithCompanies = 0;
}
