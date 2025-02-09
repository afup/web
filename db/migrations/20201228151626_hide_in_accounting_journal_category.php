<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class HideInAccountingJournalCategory extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE compta_categorie ADD COLUMN hide_in_accounting_journal_at DATETIME DEFAULT NULL');
        $this->execute('UPDATE compta_categorie SET hide_in_accounting_journal_at = NOW() WHERE categorie LIKE "Banque - Espece"');
        $this->execute('UPDATE compta_categorie SET hide_in_accounting_journal_at = NOW() WHERE categorie LIKE "Banque - Paypal"');
    }
}
