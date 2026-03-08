<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class ArticleNonNullableColumns extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_site_article')
            ->changeColumn('titre', 'text', [
                'null' => false,
            ])
            ->changeColumn('contenu', 'text', [
                'null' => false,
                'limit' => MysqlAdapter::TEXT_LONG,
            ])
            ->changeColumn('raccourci', 'string', [
                'null' => false,
            ])
            ->changeColumn('id_site_rubrique', 'integer', [
                'null' => false,
            ])
            ->changeColumn('type_contenu', 'string', [
                'null' => false,
                'limit' => 30,
                'default' => 'markdown',
            ])
            ->update();
    }
}
