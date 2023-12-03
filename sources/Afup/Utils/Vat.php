<?php

namespace Afup\Site\Utils;

class Vat
{
    const VAT_APPLICATION_DATE = "2024-01-01";

    public static function isSubjectedToVat(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d') >= self::VAT_APPLICATION_DATE;
    }

    public static function getRoundedWithoutVatPriceFromPriceWithVat($priceWithVat, $vatRate)
    {
        return round($priceWithVat / (1 + $vatRate), 2);
    }
}
