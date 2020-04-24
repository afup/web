<?php

use Phinx\Migration\AbstractMigration;

class TarifEventDescription extends AbstractMigration
{
    public function change()
    {
        $this->execute('alter table afup_forum_tarif_event modify description varchar(1024) not null');
    }
}
