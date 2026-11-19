<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Personnes des conférenciers de Conferenciers.php. La fiche 5 reste volontairement non rattachée.
 */
class SpeakerPersons extends AbstractSeed
{
    #[\Override]
    public function getDependencies(): array
    {
        return ['Conferenciers'];
    }

    public function run(): void
    {
        $persons = [
            1 => ['slug' => 'geoffrey-bachelet', 'speakers' => [1]],
            2 => ['slug' => 'adrien-gallou', 'speakers' => [2]],
            3 => ['slug' => 'john-doe', 'speakers' => [3]],
            4 => ['slug' => 'marie-ancienne', 'speakers' => [Conferenciers::ID_CONFERENCIER_ANCIEN]],
        ];

        $this->execute('UPDATE afup_conferenciers SET person_id = NULL');
        $this->execute('DELETE FROM afup_speaker_person');

        $this->table('afup_speaker_person')
            ->insert(array_map(
                static fn(int $id, array $person): array => ['id' => $id, 'slug' => $person['slug']],
                array_keys($persons),
                $persons,
            ))
            ->save();

        foreach ($persons as $id => $person) {
            $this->execute(sprintf(
                'UPDATE afup_conferenciers SET person_id = %d WHERE conferencier_id IN (%s)',
                $id,
                implode(',', $person['speakers']),
            ));
        }
    }
}
