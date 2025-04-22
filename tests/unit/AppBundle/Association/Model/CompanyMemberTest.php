<?php

declare(strict_types=1);

namespace AppBundle\Tests\Association\Model;

use AppBundle\Association\Model\CompanyMember;
use PHPUnit\Framework\TestCase;

class CompanyMemberTest extends TestCase
{
    /** @dataProvider companies */
    public function testMembershipFee(CompanyMember $companyMember, float $expectedAmount): void
    {
        self::assertEquals($expectedAmount, $companyMember->getMembershipFee());
    }

    public function companies(): array
    {
        return [
            'null' => [(new CompanyMember()), AFUP_COTISATION_PERSONNE_MORALE],
            'under' => [(new CompanyMember())->setMaxMembers(2), AFUP_COTISATION_PERSONNE_MORALE],
            'equal' => [(new CompanyMember())->setMaxMembers(3), AFUP_COTISATION_PERSONNE_MORALE],
            'just over' => [(new CompanyMember())->setMaxMembers(4), 2 * AFUP_COTISATION_PERSONNE_MORALE],
            'over' => [(new CompanyMember())->setMaxMembers(6), 2 * AFUP_COTISATION_PERSONNE_MORALE],
        ];
    }

}
