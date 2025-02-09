<?php

declare(strict_types=1);

namespace AppBundle\Compta\Importer;

class Operation
{
    const DEBIT = 'debit';
    const CREDIT = 'credit';

    private $dateEcriture;
    private $description;
    private $montant;
    private $type;
    private $numeroOperation;

    /**
     * Operation constructor.
     *
     * @param string $dateEcriture
     * @param string $description
     * @param float $montant
     * @param string $type
     * @param string $numeroOperation
     */
    public function __construct($dateEcriture, $description, $montant, $type, $numeroOperation)
    {
        $this->dateEcriture = $dateEcriture;
        $this->description = $description;
        $this->montant = $montant;
        $this->type = $type;
        $this->numeroOperation = $numeroOperation;
    }

    /**
     * @return string
     */
    public function getDateEcriture()
    {
        return $this->dateEcriture;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getNumeroOperation()
    {
        return $this->numeroOperation;
    }

    public function isCredit(): bool
    {
        return self::CREDIT === $this->getType();
    }
}
