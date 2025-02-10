<?php

declare(strict_types=1);

namespace AppBundle\Compta\BankAccount;

class BankAccountFactory
{
    public function createApplyableAt(\DateTimeInterface $applicationDate = null): BankAccount
    {
        $comparisonDate = new \Datetime('2023-01-01');
        $comparisonDate->setTime(0, 0, 1);

        if (!$applicationDate instanceof \DateTimeInterface || $applicationDate > $comparisonDate) {
            return new BankAccount(
                '10278',
                '06076',
                '00020707701',
                '70',
                "CCM PARIS MAGENTA GARE DE L'EST",
                'CMCIFR2A',
                'FR76 1027 8060 7600 0207 0770 170'
            );
        }

        return new BankAccount(
            '17515',
            '90000',
            '04045168667',
            '70',
            'CE ILE DE FRANCE PARIS',
            'CEPAFRPP751',
            'FR76 1751 5900 0004 0451 6866 770'
        );
    }
}
