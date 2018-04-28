<?php

namespace AppBundle\Event\Ticket;

use AppBundle\Event\Model\Event;
use AppBundle\Offices\OfficeFinder;

class RegistrationsExportGenerator
{

    /**
     * @var OfficeFinder
     */
    private $officeFinder;

    /**
     * @param OfficeFinder $officeFinder
     */
    public function __construct(OfficeFinder $officeFinder)
    {
        $this->officeFinder = $officeFinder;
    }

    /**
     * @param Event $event
     * @param \SplFileObject $toFile
     */
    public function export(Event $event, \SplFileObject $toFile)
    {
        $columns = [
            'id',
            'reference',
            'prenom',
            'nom',
            'societe',
            'tags',
            'type_pass',
            'email',
            'member_since',
            'office'
        ];

        $toFile->fputcsv($columns);

        foreach ($this->officeFinder->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow);
        }
    }
}
