<?php

use Phinx\Migration\AbstractMigration;

class TalkOpenfeedback extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_sessions ADD openfeedback_path varchar(255) DEFAULT NULL");
    }
}
