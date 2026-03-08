<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

final class LegacyConnectionFactory
{
    private Base_De_Donnees $bdd;

    public function __invoke(): Base_De_Donnees
    {
        if (!isset($this->bdd)) {
            if (isset($GLOBALS['AFUP_DB']) === false) {
                throw new \RuntimeException('Could not find the legacy database connexion');
            }

            $this->bdd = $GLOBALS['AFUP_DB'];
        }

        return $this->bdd;
    }
}
