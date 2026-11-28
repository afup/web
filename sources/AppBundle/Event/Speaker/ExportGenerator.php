<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\Ting\Query\QueryException;

class ExportGenerator
{
    public function __construct(
        private readonly TalkRepository $talkRepository,
        private readonly SpeakerRepository $speakerRepository,
    ) {}

    /**
     *
     * @throws QueryException
     */
    public function export(Event $event, \SplFileObject $toFile): void
    {
        $columns = [
            'nom_prenom',
            'email',
            'retenu',
            'mention_majoritaire_max',
            'qui_envoie_le_mail',
            'personne_hors_ville',
            'mentoring_demande',
            'mail_envoye',
            'confirmation_speaker',
            'sessions_proposees',
            'id',
            'lien_bo',
            'commentaire',
        ];

        $toFile->fputcsv($columns, escape: '\\');

        foreach ($this->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow, escape: '\\');
        }
    }

    /**
     *
     * @return \Generator
     * @throws QueryException
     */
    private function getFromRegistrationsOnEvent(Event $event)
    {
        /** @var list<Talk> $talks */
        $talks = iterator_to_array($this->talkRepository->getAllByEvent($event), false);
        $speakersByTalk = $this->speakerRepository->findIndexedByTalkIds(array_map(static fn(Talk $talk): int => (int) $talk->getId(), $talks));

        $talksBySpeakers = [];

        foreach ($talks as $talk) {
            foreach ($speakersByTalk[(int) $talk->getId()] ?? [] as $speaker) {
                $talksBySpeakers[$speaker->id]['speaker'] = $speaker;
                $talksBySpeakers[$speaker->id]['talks'][(int) $talk->getId()] = $talk;
            }
        }

        foreach ($talksBySpeakers as $talksBySpeaker) {
            yield $this->prepareLine($talksBySpeaker['speaker'], array_values($talksBySpeaker['talks']));
        }
    }

    private function prepareLine(Speaker $speaker, array $talks): array
    {
        $hasDemandeMentoring = false;
        $labels = [];
        /** @var Talk $talk */
        foreach ($talks as $talk) {
            if ($talk->getNeedsMentoring()) {
                $hasDemandeMentoring = true;
            }

            $labels[] = $talk->getTitle();
        }

        return [
            'nom_prenom' => $speaker->label,
            'email' => $speaker->email,
            'retenu' => '',
            'mention_majoritaire_max' => '',
            'qui_envoie_le_mail' => '',
            'personne_hors_ville' => '',
            'mentoring_demande' => $hasDemandeMentoring ? 'oui' : 'non',
            'mail_envoye' => '',
            'confirmation_speaker' => '',
            'sessions_proposees' => implode(PHP_EOL, $labels),
            'id' => $speaker->id,
            'lien_bo' => '',
            'commentaire' => '',
        ];
    }
}
