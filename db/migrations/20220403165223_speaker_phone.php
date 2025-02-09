<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class SpeakerPhone extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD phone_number varchar(20) DEFAULT NULL");
    }
}
