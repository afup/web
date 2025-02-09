<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class PlanningAnnouncement extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `date_annonce_planning` int(11) unsigned DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
