<?php

namespace AppBundle\Compta\Importer;

use AppBundle\Model\ComptaCompte;

class CreditMutuel implements Importer
{
    const CODE = 'CMUT';

    /**
     * @var \SplFileObject
     */
    private $file;

    public function initialize($filePath)
    {
        $this->file = new \SplFileObject($filePath, 'r');
        $this->file->setCsvControl(';');
        $this->file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        $this->file->rewind();
        $firstLine = $this->file->current();

        if (!is_array($firstLine)) {
            return false;
        }

        if (count($firstLine) === 6 && $firstLine[1] === 'Date de valeur') {
            return true;
        }

        return false;
    }

    /**
     * @return Operation[]
     */
    public function extract()
    {
        $nbLineByDate = [];
        foreach ($this->file as $i => $data) {
            if ($i === 0) {
                // ignore header ligne
                continue;
            }

            if (!$data || count($data) !== 6) {
                continue;
            }

            $dateEcriture = implode('-', array_reverse(explode('/', $data[0])));
            if (!isset($nbLineByDate[$dateEcriture])) {
                $nbLineByDate[$dateEcriture] = 0;
            }
            $nbLineByDate[$dateEcriture]++;

            $description = $data[4];
            $description = implode(' ', array_filter(explode(' ', $description)));

            if ('' === $data[3]) {
                $montant = abs(str_replace(',', '.', $data[2]));
                $type = Operation::DEBIT;
            } else {
                $montant = abs(str_replace(',', '.', $data[3]));
                $type = Operation::CREDIT;
            }

            // on doit fournir un numéro d'opération unique, ce qui permet lorsqu'on réimporte un fichier
            // de ne pas dupliquer les écritures.
            // Malheureusement le cmut, contrairement  à la caisse d'epargne, n'a pas cette clé dans le fichier.
            // Donc on va sortir ça avec un sha1 de la ligne et croiser les doigts pour que l'ordonnancement soit le même.
            // En théorie ça devrait le faire, le solde faisant partie de l'export, mais il ne faut sous-estimer personne
            $numeroOperation = substr(sha1(implode($data)), 0, 20);

            yield new Operation($dateEcriture, $description, $montant, $type, $numeroOperation);
        }
    }

    public function getCompteId()
    {
        return ComptaCompte::COURANT_CMUT;
    }
}
