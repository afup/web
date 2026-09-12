<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddBilleteriePrivee extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_forum_billeterie_privee')
            ->addColumn('id_forum', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('nom', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('mot_de_passe', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('id_tarif', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('prix', 'float', ['null' => false])
            ->addColumn('max_places', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('date_debut', 'datetime', ['null' => false])
            ->addColumn('date_fin', 'datetime', ['null' => false])
            ->addColumn('created_on', 'datetime', ['null' => false])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['id_forum'])
            ->save()
        ;
    }
}
