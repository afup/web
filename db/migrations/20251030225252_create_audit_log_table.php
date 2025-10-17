<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAuditLogTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_audit_log')
            ->addColumn('message', 'string', [
                'limit' => 500,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer')
            ->addColumn('route', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => '',
            ])
            ->create();
    }
}
