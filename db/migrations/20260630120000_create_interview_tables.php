<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInterviewTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('interview')
            ->addColumn('event_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('date_publication', 'datetime', ['null' => false])
            ->addColumn('wordpress_post_id', 'integer', [
                'null' => true,
                'default' => null,
            ])
            ->create();

        $this->table('interview_question')
            ->addColumn('interview_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('position', 'integer', [
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('question', 'text', ['null' => false])
            ->addColumn('reponse', 'text', ['null' => false])
            ->addForeignKey('interview_id', 'interview', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        // One interview can group several speakers, and a speaker belongs to at most one
        // interview (enforced by the unique index on speaker_id).
        $this->table('interview_speaker')
            ->addColumn('interview_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('speaker_id', 'integer', [
                'null' => false,
                'signed' => false,
            ])
            ->addForeignKey('interview_id', 'interview', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addIndex('speaker_id', ['unique' => true])
            ->create();

        $this->table('afup_forum')
            ->addColumn('interviews_wp_category_id', 'integer', ['null' => true])
            ->addColumn('interviews_intro', 'text', ['null' => true])
            ->addColumn('interviews_cta_text', 'text', ['null' => true])
            ->save();
    }
}
