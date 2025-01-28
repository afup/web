<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TicketsNearestOffice extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_inscription_forum`
ADD `nearest_office` varchar(50) DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
