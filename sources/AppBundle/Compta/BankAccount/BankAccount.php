<?php

declare(strict_types=1);

namespace AppBundle\Compta\BankAccount;

class BankAccount
{
    /**
     * @param string $etablissement
     * @param string $guichet
     * @param string $compte
     * @param string $cle
     * @param string $domicialisation
     * @param string $bic
     * @param string $iban
     */
    public function __construct(
        private $etablissement,
        private $guichet,
        private $compte,
        private $cle,
        private $domicialisation,
        private $bic,
        private $iban,
    ) {
    }

    /**
     * @return string
     */
    public function getEtablissement()
    {
        return $this->etablissement;
    }

    /**
     * @return string
     */
    public function getGuichet()
    {
        return $this->guichet;
    }

    /**
     * @return string
     */
    public function getCompte()
    {
        return $this->compte;
    }

    /**
     * @return string
     */
    public function getCle()
    {
        return $this->cle;
    }

    /**
     * @return string
     */
    public function getDomicialisation()
    {
        return $this->domicialisation;
    }

    /**
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }
}
