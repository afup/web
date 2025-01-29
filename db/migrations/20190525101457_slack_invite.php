<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class SlackInvite extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE afup_personnes_physiques ADD slack_invite_status TINYINT NOT NULL DEFAULT 0');
    }
}
