<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MakeTransactionPaymentTypeNullable extends AbstractMigration
{
    public function up(): void
    {
        $this->table('compta')
            ->changeColumn('idmode_regl', 'integer', [
                'limit' => 5,
                'null' => true,
                'default' => null,
            ])
            ->update();

        $this->execute('UPDATE compta SET idmode_regl = NULL WHERE idmode_regl = 0');
    }

    public function down(): void
    {
        $this->execute('UPDATE compta SET idmode_regl = 0 WHERE idmode_regl IS NULL');

        $this->table('compta')
            ->changeColumn('idmode_regl', 'integer', [
                'limit' => 5,
                'null' => false,
                'default' => 0,
            ])
            ->update();
    }
}
