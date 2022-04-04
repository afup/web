<?php


use Phinx\Migration\AbstractMigration;

class SpeakerPhone extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE afup_conferenciers ADD phone_number varchar(20) DEFAULT NULL");
    }
}
