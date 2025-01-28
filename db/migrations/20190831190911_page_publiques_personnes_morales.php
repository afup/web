<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class PagePubliquesPersonnesMorales extends AbstractMigration
{
    public function change(): void
    {
        $sql = <<<EOF
ALTER TABLE `afup_personnes_morales`
  ADD `public_profile_enabled` tinyint DEFAULT 0,
  ADD `description` longtext DEFAULT NULL,
  ADD `logo_url` varchar(255) DEFAULT NULL,
  ADD `website_url` varchar(255) DEFAULT NULL,
  ADD `contact_page_url` varchar(255) DEFAULT NULL,
  ADD `careers_page_url` varchar(255) DEFAULT NULL,
  ADD `twitter_handle` varchar(255) DEFAULT NULL,
  ADD `related_afup_offices` longtext DEFAULT NULL
;
EOF;
        $this->execute($sql);
    }
}
