<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class PersonneReferente extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_conferenciers ADD referent_person VARCHAR(255) DEFAULT NULL AFTER phone_number");
    }
}
