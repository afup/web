<?php

use Phinx\Migration\AbstractMigration;

class RendezVousMigration extends AbstractMigration
{
    public function down()
    {
        $this->execute('DROP TABLE afup_rendezvous, afup_rendezvous_inscrits, afup_rendezvous_slides');
    }
}
