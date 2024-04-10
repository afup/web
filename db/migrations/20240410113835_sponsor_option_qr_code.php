<?php


use Phinx\Migration\AbstractMigration;

class SponsorOptionQrCode extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('afup_forum_sponsors_tickets');
        $table->addColumn('qr_codes_scanner', 'boolean', [
            'null' => false,
            'default' => false,
        ])->update();
    }
}
