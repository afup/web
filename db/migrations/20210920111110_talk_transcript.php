<?php

use Phinx\Migration\AbstractMigration;

class TalkTranscript extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_sessions ADD transcript MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL");
    }
}
