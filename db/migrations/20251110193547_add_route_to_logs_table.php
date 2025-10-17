<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddRouteToLogsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_logs')
            ->addColumn('route', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }
}
