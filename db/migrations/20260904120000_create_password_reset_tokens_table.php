<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePasswordResetTokensTable extends AbstractMigration
{
    public function change(): void
    {
        // user_id est la clé primaire : un membre ne peut avoir qu'un seul jeton
        // valide à un instant T, l'invariant est porté par le schéma.
        $this
            ->table('afup_password_reset_tokens', ['id' => false, 'primary_key' => ['user_id']])
            ->addColumn('user_id', 'integer', ['signed' => false, 'null' => false])
            // SHA-256 du jeton : le jeton en clair n'existe que dans l'e-mail.
            ->addColumn('token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('created_at', 'datetime', ['null' => false])
            ->addColumn('expires_at', 'datetime', ['null' => false])
            ->addIndex('token_hash', ['unique' => true])
            ->create()
        ;
    }
}
