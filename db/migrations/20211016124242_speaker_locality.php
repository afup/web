<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SpeakerLocality extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD ville varchar(255) DEFAULT NULL AFTER societe");
    }
}
