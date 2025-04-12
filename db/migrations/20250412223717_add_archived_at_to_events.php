<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddArchivedAtToEvents extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_forum')
            ->addColumn('archived_at', 'timestamp', [
                'null' => true,
            ])
            ->save();
    }
}
