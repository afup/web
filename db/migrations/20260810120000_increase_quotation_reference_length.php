<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class IncreaseQuotationReferenceLength extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_compta_facture_details')
            ->changeColumn('ref', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->update();
    }
}
