<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

class Vat
{
    const VAT_APPLICATION_DATE = "2024-01-01";

    public static function isSubjectedToVat(\DateTimeInterface $date): bool
    {
        return $date->format('Y-m-d') >= self::VAT_APPLICATION_DATE;
    }

    public static function getRoundedWithoutVatPriceFromPriceWithVat($priceWithVat, $vatRate): float
    {
        return round($priceWithVat / (1 + $vatRate), 2);
    }

    public static function getRoundedWithVatPriceFromPriceWithoutVat($priceWithoutVat, $vatRate): float
    {
        return round($priceWithoutVat * (1 + $vatRate), 2);
    }
}
