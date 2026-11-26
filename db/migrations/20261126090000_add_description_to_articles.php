<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDescriptionToArticles extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_site_article')
            ->addColumn('description', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null,
                'after' => 'chapeau',
            ])
            ->save()
        ;
    }
}
