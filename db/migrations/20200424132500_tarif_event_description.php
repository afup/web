<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class TarifEventDescription extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('alter table afup_forum_tarif_event modify description varchar(1024) not null');
    }
}
