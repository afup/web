<?php


namespace AppBundle;


use Afup\Site\Utils\Base_De_Donnees;

class LegacyModelFactory
{
    /**
     * @var Base_De_Donnees
     */
    private $bdd;

    public function createObject($type)
    {
        if (class_exists($type) === false) {
            throw new \RuntimeException(sprintf('Could not find object of type "%s". Did you forgot to require the old autoload ?', $type));
        }
        return new $type($this->getBdd());
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
