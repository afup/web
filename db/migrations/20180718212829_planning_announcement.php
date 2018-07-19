<?php

use Phinx\Migration\AbstractMigration;

class PlanningAnnouncement extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `date_annonce_planning` int(11) unsigned DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
