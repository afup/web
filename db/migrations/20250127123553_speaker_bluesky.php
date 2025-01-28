<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SpeakerBluesky extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD bluesky varchar(255) DEFAULT NULL AFTER mastodon");
    }
}
