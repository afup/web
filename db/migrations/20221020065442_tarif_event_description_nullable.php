<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TarifEventDescriptionNullable extends AbstractMigration
{
    public function change(): void
    {
        $this->query("ALTER TABLE afup_forum_tarif_event MODIFY description VARCHAR(1024) DEFAULT NULL");
    }
}
