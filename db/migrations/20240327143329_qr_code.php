<?php


use Phinx\Migration\AbstractMigration;

class QrCode extends AbstractMigration
{
    public function change()
    {
        $tableInscription = $this->table('afup_inscription_forum');
        $tableInscription->addColumn('qr_code', 'string', ['limit' => 10, 'default' => null, 'null' => true])
            ->update();

        $tableScan = $this->table('afup_forum_sponsor_scan');
        $tableScan->addColumn('sponsor_ticket_id', 'integer')
            ->addColumn('ticket_id', 'integer')
            ->addColumn('created_on', 'datetime')
            ->create();
    }
}
