<?php


use Phinx\Migration\AbstractMigration;

class QrCode extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
ALTER TABLE `afup_inscription_forum`
    ADD `qr_code` VARCHAR(10);
EOF;
        $this->execute($sql);

        $sql = <<<EOF
CREATE TABLE IF NOT EXISTS `afup_forum_sponsor_scan` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `sponsor_ticket_id` INT(11) NOT NULL,
        `ticket_id` INT(11) NOT NULL,
        `created_on` DATETIME NOT NULL,
        `deleted_on` DATETIME,
        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOF;
        $this->execute($sql);
    }
}
