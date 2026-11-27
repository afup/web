<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSlackMembresIdCanalToAntenne extends AbstractMigration
{
    public function change(): void
    {
        $this
            ->table('afup_antenne')
            ->addColumn('slack_membres_id_canal', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => null,
                'after' => 'meetup_id_externe',
            ])
            ->save()
        ;
    }
}
