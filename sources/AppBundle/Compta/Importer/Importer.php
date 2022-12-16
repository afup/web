<?php

namespace AppBundle\Compta\Importer;

interface Importer
{
    /**
     * @param string $filePath
     * @return void
     */
    public function initialize($filePath);

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
