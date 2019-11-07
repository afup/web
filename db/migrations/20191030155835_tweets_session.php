<?php

use Phinx\Migration\AbstractMigration;

class TweetsSession extends AbstractMigration
{
    public function change()
    {
        $sql = <<<SQL
ALTER TABLE `afup_sessions`
ADD `tweets` text DEFAULT NULL
;
SQL;
        $this->execute($sql);
    }
}
