<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

use AppBundle\Model\ComptaCompte;

class CreditMutuelLivret extends CreditMutuel
{
    const CODE = 'CMUTLIVRET';

    public function getCompteId()
    {
        return ComptaCompte::LIVRET_CMUT;
    }
}
