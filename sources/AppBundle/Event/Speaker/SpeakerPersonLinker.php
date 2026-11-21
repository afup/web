<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Entity\Repository\SpeakerPersonRepository;
use AppBundle\Event\Entity\SpeakerPerson;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use Cocur\Slugify\Slugify;

/**
 * Rattache une ligne conférencier (une par édition) à la personne qu'elle représente.
 */
final readonly class SpeakerPersonLinker
{
    public function __construct(
        private SpeakerRepository $speakerRepository,
        private SpeakerPersonRepository $speakerPersonRepository,
    ) {}

    /**
     * Personne déjà connue pour cette ligne, sans rien créer : la sienne, sinon celle de son compte GitHub.
     */
    public function findPersonId(Speaker $speaker): ?int
    {
        if ($speaker->getPersonId() !== null) {
            return $speaker->getPersonId();
        }

        $githubUserId = $speaker->getUser();

        return $githubUserId ? $this->speakerRepository->findLatestPersonIdByGithubUser($githubUserId) : null;
    }

    /**
     * À appeler avant d'enregistrer une nouvelle ligne : rattache la personne du compte GitHub
     * ou, à défaut, en crée une. Ne sauvegarde pas la ligne.
     */
    public function ensurePerson(Speaker $speaker): void
    {
        if ($speaker->getPersonId() !== null) {
            return;
        }

        $speaker->setPersonId(
            $this->findPersonId($speaker)
            ?? $this->createPerson($speaker->getFirstname() . ' ' . $speaker->getLastname())->id,
        );
    }

    /**
     * Le slug vient de « prénom nom », suffixé -2, -3… s'il est déjà pris. Il ne change plus ensuite.
     */
    public function createPerson(string $label): SpeakerPerson
    {
        $base = new Slugify()->slugify($label);
        if ($base === '') {
            $base = 'conferencier';
        }

        $slug = $base;
        for ($suffix = 2; $this->speakerPersonRepository->slugExists($slug); $suffix++) {
            $slug = $base . '-' . $suffix;
        }

        $person = new SpeakerPerson($slug);
        $this->speakerPersonRepository->save($person);

        return $person;
    }

    public function findRowInEvent(int $personId, Event $event): ?Speaker
    {
        return $this->speakerRepository->findOneByPersonInEvent($personId, $event);
    }
}
