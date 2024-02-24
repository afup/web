<?php

use Phinx\Migration\AbstractMigration;

class Sessions extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
CREATE TABLE `sessions` (
    `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_lifetime` INTEGER UNSIGNED NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL,
    INDEX `sessions_sess_lifetime_idx` (`sess_lifetime`)
) COLLATE utf8mb4_bin, ENGINE = InnoDB;
EOF;
        $this->execute($sql);
    }
}
