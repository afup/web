<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddMicTypeToSpeakers extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD mic_type ENUM('handheld', 'headset') DEFAULT NULL AFTER bluesky");
    }
}
