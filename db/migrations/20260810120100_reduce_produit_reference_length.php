<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReduceProduitReferenceLength extends AbstractMigration
{
    public function change(): void
    {
        $this->table('compta_produit')
            ->changeColumn('reference', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->update();
    }
}
