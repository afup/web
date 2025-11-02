<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MemberType: int
{
    case MemberPhysical = 0;
    case MemberCompany = 1;
    case AllMembers = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MemberPhysical => 'Personne physique',
            self::MemberCompany => 'Personne morale',
            self::AllMembers => 'Tout type de membre',
        };
    }
}
