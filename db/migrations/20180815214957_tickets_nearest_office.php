<?php

use Phinx\Migration\AbstractMigration;

class TicketsNearestOffice extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE `afup_inscription_forum`
ADD `nearest_office` varchar(50) DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
