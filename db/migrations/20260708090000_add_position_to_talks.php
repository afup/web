<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPositionToTalks extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_sessions')
            ->addColumn('position', 'integer', ['null' => true])
            ->save()
        ;
    }
}
