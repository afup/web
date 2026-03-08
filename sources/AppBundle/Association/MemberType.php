<?php

declare(strict_types=1);

namespace AppBundle\Association;

enum MemberType: int
{
    case MemberPhysical = 0;
    case MemberCompany = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::MemberPhysical => 'Personne physique',
            self::MemberCompany => 'Personne morale',
        };
    }
}
