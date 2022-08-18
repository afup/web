<?php


use Phinx\Migration\AbstractMigration;

class SiteFeuilleTexteImageAlt extends AbstractMigration
{
    public function change()
    {
        $this->query("ALTER TABLE `afup_site_feuille` ADD `image_alt` VARCHAR(255) DEFAULT NULL AFTER `image`");
    }
}
