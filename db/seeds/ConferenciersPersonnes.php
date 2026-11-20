<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Personnes des conférenciers de Conferenciers.php. La fiche 5 reste volontairement non rattachée.
 */
class ConferenciersPersonnes extends AbstractSeed
{
    #[\Override]
    public function getDependencies(): array
    {
        return ['Conferenciers'];
    }

    public function run(): void
    {
        $personnes = [
            1 => ['slug' => 'geoffrey-bachelet', 'speakers' => [1]],
            2 => ['slug' => 'adrien-gallou', 'speakers' => [2]],
            3 => ['slug' => 'john-doe', 'speakers' => [3]],
            4 => ['slug' => 'marie-ancienne', 'speakers' => [Conferenciers::ID_CONFERENCIER_ANCIEN]],
        ];

        $this->execute('UPDATE afup_conferenciers SET personne_id = NULL');
        $this->execute('DELETE FROM afup_conferenciers_personnes');

        $this->table('afup_conferenciers_personnes')
            ->insert(array_map(
                static fn(int $id, array $personne): array => ['id' => $id, 'slug' => $personne['slug']],
                array_keys($personnes),
                $personnes,
            ))
            ->save();

        foreach ($personnes as $id => $personne) {
            $this->execute(sprintf(
                'UPDATE afup_conferenciers SET personne_id = %d WHERE conferencier_id IN (%s)',
                $id,
                implode(',', $personne['speakers']),
            ));
        }
    }
}
