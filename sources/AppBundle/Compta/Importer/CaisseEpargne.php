<?php

namespace AppBundle\Compta\Importer;

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
        if ('Code de la banque' === !substr($firstLine[0], 0, 17)) {
            return false;
        }

        return true;
    }

    public function extract()
    {
        foreach ($this->file as $i => $data) {
            if ($i <= 4) {
                continue;
            }

            if (count($data) !== 7) {
                continue;
            }

            yield $data;
        }
    }
}
