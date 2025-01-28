<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TalkOpenfeedback extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_sessions ADD openfeedback_path varchar(255) DEFAULT NULL");
    }
}
