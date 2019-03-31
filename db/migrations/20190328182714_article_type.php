<?php


use Phinx\Migration\AbstractMigration;

class ArticleType extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE afup_site_article ADD type_contenu VARCHAR(30) DEFAULT 'markdown' AFTER contenu");
        $this->execute("UPDATE afup_site_article SET type_contenu = 'html'");
    }
}
