<?php

namespace Afup\Site\Utils;

class Vat
{
    public static function isSubjectedToVat(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d') >= '2024-01-01';
    }

    public static function getRoundedWithoutVatPriceFromPriceWithVat($priceWithVat, $vatRate)
    {
        return round($priceWithVat / (1 + $vatRate), 2);
    }
}
