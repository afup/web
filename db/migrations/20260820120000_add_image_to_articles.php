<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddImageToArticles extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_site_article')
            ->addColumn('image', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null,
                'after' => 'contenu',
            ])
            ->save()
        ;
    }
}
