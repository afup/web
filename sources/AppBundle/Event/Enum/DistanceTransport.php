<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

enum DistanceTransport: int
{
    case Moins25 = 0;
    case De25A50 = 25;
    case De50A100 = 50;
    case De100A500 = 100;
    case De500A1000 = 500;
    case Plus1000 = 1000;

    public function label(): string
    {
        return match ($this) {
            self::Moins25 => '< 25 km',
            self::De25A50 => 'entre 25 et 50 km',
            self::De50A100 => 'entre 50 et 100 km',
            self::De100A500 => 'entre 100 et 500 km',
            self::De500A1000 => 'entre 500 et 1000 km',
            self::Plus1000 => '> 1000 km',
        };
    }
}
