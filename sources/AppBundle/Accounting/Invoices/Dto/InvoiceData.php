<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Invoices\Dto;

final readonly class InvoiceData
{
    public function __construct(
        public string $recipient,
        public string $address,
        public string $zipcode,
        public string $city,
        public string $patternPrefix,
    ) {}
}
