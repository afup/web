<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class TalkVerbatim extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_sessions ADD verbatim MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL");
    }
}
