<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class AddGenreToUsersSpeakersAndTickets extends AbstractMigration
{
    public function change(): void
    {
        $this->table('afup_personnes_physiques')
            ->addColumn('genre', 'string', [
                'null' => true,
                'after' => 'civilite',
                'limit' => MysqlAdapter::TEXT_TINY,
            ])
            ->save();

        $this->query(<<<SQL
            UPDATE afup_personnes_physiques
            SET genre = CASE civilite
                WHEN 'Mme' THEN 'femme'
                WHEN 'M.'  THEN 'homme'
                WHEN '0'   THEN 'homme'
                WHEN '1'   THEN 'femme'
                WHEN '2'   THEN 'femme'
                ELSE NULL
            END;
        SQL);

        $this->table('afup_conferenciers')
            ->addColumn('genre', 'string', [
                'null' => true,
                'after' => 'civilite',
                'limit' => MysqlAdapter::TEXT_TINY,
            ])
            ->save();

        $this->query(<<<SQL
            UPDATE afup_conferenciers
            SET genre = CASE civilite
                WHEN 'Mme' THEN 'femme'
                WHEN 'M.'  THEN 'homme'
                WHEN 'M'   THEN 'homme'
                ELSE NULL
            END;
        SQL);

        $this->table('afup_inscription_forum')
            ->addColumn('genre', 'string', [
                'null' => true,
                'after' => 'civilite',
                'limit' => MysqlAdapter::TEXT_TINY,
            ])
            ->save();

        $this->query(<<<SQL
            UPDATE afup_inscription_forum
            SET genre = CASE civilite
                WHEN 'Mme' THEN 'femme'
                WHEN 'M.'  THEN 'homme'
                WHEN 'M'   THEN 'homme'
                ELSE NULL
            END;
        SQL);
    }
}
