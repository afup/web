<?php

declare(strict_types=1);


use Phinx\Migration\AbstractMigration;

class HideInAccountingJournalEvent extends AbstractMigration
{
    public function change(): void
    {
        $this->execute('ALTER TABLE compta_evenement ADD COLUMN hide_in_accounting_journal_at DATETIME DEFAULT NULL');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement LIKE "AFUP Day 201%"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Barcamp"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Drupagora 2012"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement LIKE "Forum 201%"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement LIKE "Forum 200%"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement LIKE "PHP Tour%"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement LIKE "Salon Solution Linux%"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Journee Dev"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Livre blanc"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Open Source Summit 2015"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "Symfony live 2009"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "ZendCon 2013"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "PHP TV"');
        $this->execute('UPDATE compta_evenement SET hide_in_accounting_journal_at = NOW() WHERE evenement = "RV AFUP"');
    }
}
