<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DeleteAfupForumPartenairesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_forum_partenaires')->drop()->save();
    }
}
