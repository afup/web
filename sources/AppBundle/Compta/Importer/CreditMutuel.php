<?php

namespace AppBundle\Compta\Importer;

class CreditMutuel implements Importer
{
    const CODE = 'CMUT';

    public function initialize($filePath)
    {
        // TODO: Implement initialize() method.
    }

    public function validate()
    {
        return true;
    }

    public function extract()
    {
        return [];
    }
}
