<?php

namespace AppBundle\Compta\Importer;

use AppBundle\Model\ComptaCompte;

class CaisseEpargne implements Importer
{
    const CODE = 'CE';

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var \SplFileObject
     */
    private $file;

    public function __construct()
    {
        $this->filePath;
    }

    public function initialize($filePath)
    {
        $this->file = new \SplFileObject($filePath, 'r');
        $this->file->setCsvControl(';');
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
    }

    public function validate()
    {
        $this->file->rewind();
        $firstLine = $this->file->current();

        if (!is_array($firstLine)) {
            return false;
        }

        // On vérifie la première ligne
        if (0 !== strpos($firstLine[0], 'Code de la banque')) {
            return false;
        }

        return true;
    }

    /**
     * @return Operation[]
     */
    public function extract()
    {
        foreach ($this->file as $i => $data) {
            if ($i <= 4) {
                continue;
            }

            if (count($data) !== 7) {
                continue;
            }

            $dateEcriture = '20' . implode('-', array_reverse(explode('/', $data[0])));
            if ('' === $data[5]) {
                $description = $data[2];
            } elseif (false === strpos($data[5], $data[2])) {
                $description = $data[2] . ' - ' . $data[5];
            } else {
                $description = $data[5];
            }

            // petit nettoyage pour virer les espaces multiples
            $description = implode(' ', array_filter(explode(' ', $description)));

            if ('' === $data[4]) {
                $montant = abs(str_replace(',', '.', $data[3]));
                $type = Operation::DEBIT;
            } else {
                $montant = abs(str_replace(',', '.', $data[4]));
                $type = Operation::CREDIT;
            }

            $numeroOperation = $data[1];

            yield new Operation($dateEcriture, $description, $montant, $type, $numeroOperation);
        }
    }

    public function getCompteId()
    {
        return ComptaCompte::COURANT_CE;
    }
}
