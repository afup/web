<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class MeetupEmojis extends AbstractMigration
{
    public function change(): void
    {
        $this->execute("ALTER TABLE afup_meetup CHANGE description description text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
        $this->execute("ALTER TABLE afup_meetup CHANGE title title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;");
    }
}
