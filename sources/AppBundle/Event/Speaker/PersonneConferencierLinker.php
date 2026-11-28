<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Entity\Repository\PersonneConferencierRepository;
use AppBundle\Event\Entity\PersonneConferencier;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use Cocur\Slugify\Slugify;

/**
 * Rattache une ligne conférencier (une par édition) à la personne qu'elle représente.
 */
final readonly class PersonneConferencierLinker
{
    public function __construct(
        private SpeakerRepository $speakerRepository,
        private PersonneConferencierRepository $personneConferencierRepository,
    ) {}

    /**
     * Personne déjà connue pour cette ligne, sans rien créer : la sienne, sinon celle de son compte GitHub.
     */
    public function findPersonneId(Speaker $speaker): ?int
    {
        if ($speaker->personneId !== null) {
            return $speaker->personneId;
        }

        $githubUserId = $speaker->githubUserId;

        return $githubUserId ? $this->personneConferencierRepository->findLatestIdByGithubUser($githubUserId) : null;
    }

    /**
     * À appeler avant d'enregistrer une nouvelle ligne : rattache la personne du compte GitHub
     * ou, à défaut, en crée une. Ne sauvegarde pas la ligne.
     */
    public function ensurePersonne(Speaker $speaker): void
    {
        if ($speaker->personneId !== null) {
            return;
        }

        $speaker->personneId = $this->findPersonneId($speaker)
            ?? $this->createPersonne($speaker->firstname . ' ' . $speaker->lastname)->id;
    }

    /**
     * Le slug vient de « prénom nom », suffixé -2, -3… s'il est déjà pris. Il ne change plus ensuite.
     */
    public function createPersonne(string $label): PersonneConferencier
    {
        $base = new Slugify()->slugify($label);
        if ($base === '') {
            $base = 'conferencier';
        }

        $slug = $base;
        for ($suffix = 2; $this->personneConferencierRepository->slugExists($slug); $suffix++) {
            $slug = $base . '-' . $suffix;
        }

        $personne = new PersonneConferencier($slug);
        $this->personneConferencierRepository->save($personne);

        return $personne;
    }

    public function findRowInEvent(int $personneId, Event $event): ?Speaker
    {
        $speakerId = $event->getId() !== null ? $this->personneConferencierRepository->findSpeakerIdInEvent($personneId, $event->getId()) : null;
        return $speakerId !== null ? $this->speakerRepository->find($speakerId) : null;
    }
}
