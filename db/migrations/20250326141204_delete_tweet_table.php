<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DeleteTweetTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('tweet')->drop()->save();
    }
}
