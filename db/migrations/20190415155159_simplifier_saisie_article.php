<?php


use Phinx\Migration\AbstractMigration;

class SimplifierSaisieArticle extends AbstractMigration
{
    public function change()
    {
        $this->execute("update afup_site_article set chapeau = descriptif where LENGTH(TRIM(chapeau)) = 0 and LENGTH(TRIM(descriptif));");
        $this->execute('ALTER TABLE afup_site_article DROP COLUMN surtitre');
        $this->execute('ALTER TABLE afup_site_article DROP COLUMN descriptif');
    }
}
