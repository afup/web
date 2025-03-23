<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

interface Importer
{
    public function initialize(string $filePath): void;

    public function validate(): bool;

    /**
     * @return \Generator<int, Operation>
     */
    public function extract(): \Generator;

    public function getCompteId(): int;
}
