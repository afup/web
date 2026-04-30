<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddMicTypeEnabledToForum extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_forum ADD mic_type_enabled TINYINT DEFAULT 0");
    }
}
