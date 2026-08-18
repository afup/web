<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddWordPressPostSlugToEvents extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('interview')
            ->addColumn('wordpress_post_slug', 'string', [
                'null' => true,
                'default' => null,
            ])
            ->save()
        ;
    }
}
