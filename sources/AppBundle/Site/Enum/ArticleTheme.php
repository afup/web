<?php

declare(strict_types=1);

namespace AppBundle\Site\Enum;

enum ArticleTheme: int
{
    case CycleConference = 1;
    case Antennes = 2;
    case Associatif = 3;
    case Barometre = 4;
    case AfupSoutien = 5;

    public function label(): string
    {
        return match ($this) {
            self::CycleConference => 'Cycles de conférences',
            self::Antennes => 'Antennes',
            self::Associatif => 'Associatif',
            self::Barometre => 'Baromètre',
            self::AfupSoutien => "L'AFUP soutient",
        };
    }

    /**
     * @return array<string, int>
     */
    public static function asChoicesMap(): array
    {
        $map = [];

        foreach (self::cases() as $case) {
            $map[$case->label()] = $case->value;
        }

        return $map;
    }
}
