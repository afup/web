<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class RendezVousMigration extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('DROP TABLE afup_rendezvous, afup_rendezvous_inscrits, afup_rendezvous_slides');
    }
}
