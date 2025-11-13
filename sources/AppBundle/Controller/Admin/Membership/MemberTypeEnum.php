<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Membership;

enum MemberTypeEnum: int
{
    case MEMBER_PHYSICAL = 0;
    case MEMBER_COMPAGNY = 1;
    case ALL_MEMBERS = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MEMBER_PHYSICAL => 'Personne physique',
            self::MEMBER_COMPAGNY => 'Personne morale',
            self::ALL_MEMBERS => 'Tout type de membre',
        };
    }
}
