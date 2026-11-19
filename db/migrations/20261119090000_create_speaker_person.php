<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Une personne regroupe les lignes afup_conferenciers (une par édition) d'un même conférencier.
 * Table créée vide et pas encore utilisée par le code : elle est remplie en prod avant que le
 * back-office, le CFP puis les pages publiques ne s'en servent.
 */
final class CreateSpeakerPerson extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_speaker_person', ['signed' => false])
            ->addColumn('slug', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('slug', ['unique' => true])
            ->create();

        $this->table('afup_conferenciers')
            ->addColumn('person_id', 'integer', ['null' => true, 'default' => null, 'signed' => false, 'after' => 'user_github'])
            ->addForeignKey('person_id', 'afup_speaker_person', 'id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
            ->update();
    }
}
