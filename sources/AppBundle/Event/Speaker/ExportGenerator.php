<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\Ting\Query\QueryException;

class ExportGenerator
{
    public function __construct(private readonly TalkRepository $talkRepository)
    {
    }

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

        $toFile->fputcsv($columns);

        foreach ($this->getFromRegistrationsOnEvent($event) as $row) {
            $preparedRow = [];
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    throw new \RuntimeException(sprintf('Colonne "%s" non trouvée : %s', $column, var_export($row, true)));
                }
                $preparedRow[] = $row[$column];
            }
            $toFile->fputcsv($preparedRow);
        }
    }

    /**
     *
     * @return \Generator
     * @throws QueryException
     */
    private function getFromRegistrationsOnEvent(Event $event)
    {
        $talksWithSpeakers = $this->talkRepository->getAllByEventWithSpeakers($event);

        $talksBySpeakers = [];

        foreach ($talksWithSpeakers as $talkWithSpeaker) {
            foreach ($talkWithSpeaker['.aggregation']['speaker'] as $speaker) {
                $talksBySpeakers[$speaker->getId()]['speaker'] = $speaker;
                $talksBySpeakers[$speaker->getId()]['talks'][$talkWithSpeaker['talk']->getId()] = $talkWithSpeaker['talk'];
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
            'nom_prenom' => $speaker->getLabel(),
            'email' => $speaker->getEmail(),
            'retenu' => '',
            'mention_majoritaire_max' => '',
            'qui_envoie_le_mail' => '',
            'personne_hors_ville' => '',
            'mentoring_demande' => $hasDemandeMentoring ? 'oui' : 'non',
            'mail_envoye' => '',
            'confirmation_speaker' => '',
            'sessions_proposees' => implode(PHP_EOL, $labels),
            'id' => $speaker->getId(),
            'lien_bo' => '',
            'commentaire' => '',
        ];
    }
}
