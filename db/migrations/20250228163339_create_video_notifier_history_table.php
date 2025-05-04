<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class CreateVideoNotifierHistoryTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('video_notifier_history')
            ->addColumn('talk_id', 'integer')
            ->addColumn('status_id_bluesky', 'string', [
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('status_id_mastodon', 'string', [
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => '',
            ])
            ->create();
    }
}
