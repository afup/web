<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

interface Importer
{
    /**
     * @param string $filePath
     */
    public function initialize($filePath): void;

    /**
     * @return boolean
     */
    public function validate();

    /**
     * @return Operation[]
     */
    public function extract();

    /**
     * @return int
     */
    public function getCompteId();
}
