<?php

declare(strict_types=1);

namespace AppBundle\Compta\BankAccount;

class BankAccount
{
    /**
     * @var string
     */
    private $etablissement;

    /**
     * @var string
     */
    private $guichet;

    /**
     * @var string
     */
    private $compte;

    /**
     * @var string
     */
    private $cle;

    /**
     * @var string
     */
    private $domicialisation;

    /**
     * @var string
     */
    private $bic;

    /**
     * @var string
     */
    private $iban;

    /**
     * @param string $etablissement
     * @param string $guichet
     * @param string $compte
     * @param string $cle
     * @param string $domicialisation
     * @param string $bic
     * @param string $iban
     */
    public function __construct($etablissement, $guichet, $compte, $cle, $domicialisation, $bic, $iban)
    {
        $this->etablissement = $etablissement;
        $this->guichet = $guichet;
        $this->compte = $compte;
        $this->cle = $cle;
        $this->domicialisation = $domicialisation;
        $this->bic = $bic;
        $this->iban = $iban;
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
