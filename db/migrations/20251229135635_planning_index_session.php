<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PlanningIndexSession extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_forum_planning')
            ->addIndex(['id_session'])
            ->update();
    }
}
