<?php

declare(strict_types=1);

namespace AppBundle\Accounting;

use AppBundle\Accounting\Model\Repository\InvoicingRepository;

class InvoicingNumberGenerator
{
    public function __construct(private readonly InvoicingRepository $repository) {}

    public function generateInvoiceNumber(): string
    {
        $year = (int) date('Y');

        $index = $this->repository->getNextInvoiceIndex($year);

        if ($index === null) {
            $index = $this->repository->getNextInvoiceIndex($year - 1);
            $index = $index ?? 1;
        }

        return "$year-$index";
    }

    public function generateQuotationNumber(): string
    {
        $year = (int) date('Y');

        $index = $this->repository->getNextQuotationIndex($year);

        return date('Y') . '-' . sprintf('%02d', $index ?? 1);
    }
}
