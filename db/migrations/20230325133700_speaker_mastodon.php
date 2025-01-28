<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class SpeakerMastodon extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD mastodon varchar(255) DEFAULT NULL AFTER twitter");
    }
}
