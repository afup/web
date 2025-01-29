<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class ReferentPersonEmail extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD referent_person_email VARCHAR(255) DEFAULT NULL AFTER referent_person");
    }
}
