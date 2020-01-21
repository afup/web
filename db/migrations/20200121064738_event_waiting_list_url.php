<?php

use Phinx\Migration\AbstractMigration;

class EventWaitingListUrl extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE `afup_forum`
ADD `waiting_list_url` varchar(255) DEFAULT NULL
;
SQL;

        $this->execute($sql);
    }
}
