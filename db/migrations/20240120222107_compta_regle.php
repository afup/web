<?php

use Phinx\Migration\AbstractMigration;

class ComptaRegle extends AbstractMigration
{
    public function change()
    {
        $sql = <<<EOF
CREATE TABLE compta_regle
(
    `id` tinyint(5) NOT NULL AUTO_INCREMENT,
    `label`               VARCHAR(255) NOT NULL,
    `condition`           VARCHAR(255) NOT NULL,
    `is_credit`           TINYINT(2)   NULL,
    `vat`                 VARCHAR(7)   NULL,
    `category_id`         TINYINT(5)   NULL,
    `event_id`            TINYINT(5)   NULL,
    `mode_regl_id`        TINYINT(5)   NULL,
    `attachment_required` TINYINT(2)   NULL,
    PRIMARY KEY (`id`)
);
EOF;
        $this->execute($sql);
    }
}
