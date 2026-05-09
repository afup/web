<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateComptaProduitTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('compta_produit')
            ->addColumn('reference', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('designation', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('quantite', 'integer', ['null' => true])
            ->addColumn('prix_unitaire_ht', 'float', ['null' => false])
            ->addColumn('taux_tva', 'float', ['null' => true])
            ->create();
    }
}
