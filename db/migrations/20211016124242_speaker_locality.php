<?php

use Phinx\Migration\AbstractMigration;

class SpeakerLocality extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_conferenciers ADD ville varchar(255) DEFAULT NULL AFTER societe");
    }
}
