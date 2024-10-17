<?php

declare(strict_types=1);


namespace AppBundle;

use Afup\Site\Utils\Base_De_Donnees;
use Afup\Site\Utils\Logs;

class LegacyModelFactory
{
    /**
     * @var Base_De_Donnees
     */
    private $bdd;

    /**
     * @template T of object
     *
     * @param class-string<T> $type
     *
     * @return T
     */
    public function createObject($type)
    {
        if (class_exists($type) === false) {
            throw new \RuntimeException(sprintf('Could not find object of type "%s". Did you forgot to require the old autoload ?', $type));
        }
        /**
         * We need to create a variable because some legacy objects use a pass-by-reference on this param
         */
        $bdd = $this->getBdd();

        if ($type === Logs::class) {
            Logs::initialiser($bdd, 0);
            return Logs::_obtenirInstance();
        }

        return new $type($bdd);
    }

    /**
     * @return Base_De_Donnees
     */
    private function getBdd()
    {
        if ($this->bdd === null) {
            if (isset($GLOBALS['AFUP_DB']) === false) {
                throw new \RuntimeException('Could not find the legacy database connexion');
            }
            $this->bdd = $GLOBALS['AFUP_DB'];
        }

        return $this->bdd;
    }
}
