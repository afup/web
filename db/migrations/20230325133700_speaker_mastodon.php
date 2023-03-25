<?php

use Phinx\Migration\AbstractMigration;

class SpeakerMastodon extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_conferenciers ADD mastodon varchar(255) DEFAULT NULL AFTER twitter");
    }
}
