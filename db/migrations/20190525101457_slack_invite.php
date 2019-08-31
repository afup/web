<?php


use Phinx\Migration\AbstractMigration;

class SlackInvite extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE afup_personnes_physiques ADD slack_invite_status TINYINT NOT NULL DEFAULT 0');
    }
}
