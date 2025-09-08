<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddEventThemesFeature extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_forum')
            ->addColumn('has_themes', 'boolean', ['null' => false, 'default' => false])
            ->save()
        ;
        $this
            ->table('afup_sessions')
            ->addColumn('theme', 'integer', ['null' => true])
            ->save()
        ;
        $this
            ->table('afup_conference_theme')
            ->addColumn('id_forum', 'integer', ['null' => false, 'signed' => false])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('priority', 'integer', ['null' => false, 'default' => 0])
            ->save()
        ;
    }
}
