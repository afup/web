<?php

namespace AppBundle\Compta\BankAccount;

use Afup\Site\Utils\Configuration;

class BankAccountFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createApplyableAt(\DateTimeInterface $applicationDate = null)
    {
        if (null === $applicationDate) {
            $configKey = 'rib';
        } else {
            $comparisonDate = \DateTime::createFromFormat('Y-m-d', $this->configuration->obtenir('rib_ce|valid_until'));
            $configKey = $applicationDate <= $comparisonDate ? 'rib_ce' : 'rib';
        }

        return new BankAccount(
            $this->configuration->obtenir($configKey . '|etablissement'),
            $this->configuration->obtenir($configKey . '|guichet'),
            $this->configuration->obtenir($configKey . '|compte'),
            $this->configuration->obtenir($configKey . '|cle'),
            $this->configuration->obtenir($configKey . '|domiciliation'),
            $this->configuration->obtenir($configKey . '|bic'),
            $this->configuration->obtenir($configKey . '|iban')
        );
    }
}
