<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDateFactureToCotisation extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_cotisations')
            ->addColumn('date_facture', 'timestamp', [
                'null' => true,
            ])
            ->update();
    }
}
