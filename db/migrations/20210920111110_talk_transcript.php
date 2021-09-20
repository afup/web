<?php

use Phinx\Migration\AbstractMigration;

class TalkTranscript extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_sessions ADD transcript MEDIUMTEXT DEFAULT NULL");
    }
}
