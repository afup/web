<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Matrice READ/WRITE par module (cf. affichage "Lecture seule / Gestionnaire") :
 * la chaîne niveau_modules passe de 5 à 9 caractères.
 * Index 0 à 4 (existants) : apéro, annuaire/membres, site, forum/évènement, antenne.
 * Index 5 à 8 (nouveaux, initialisés à 0) : veille, trésorerie, assemblée générale, planète.
 *
 * Encodage : 0 = '--', 1 = Lecture seule, 2 = Gestionnaire — les '2' existants
 * sont donc conservés tels quels.
 */
class NiveauModulesMatriceReadWrite extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("UPDATE afup_personnes_physiques SET niveau_modules = LPAD(niveau_modules, 9, '0') WHERE CHAR_LENGTH(niveau_modules) < 9");
    }

    public function down(): void
    {
        $this->execute('UPDATE afup_personnes_physiques SET niveau_modules = LEFT(niveau_modules, 5) WHERE CHAR_LENGTH(niveau_modules) > 5');
    }
}
