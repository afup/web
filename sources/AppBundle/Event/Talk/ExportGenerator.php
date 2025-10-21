<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use CCMBenchmark\Ting\Query\QueryException;

class ExportGenerator
{
    public function __construct(private readonly TalkRepository $talkRepository) {}

    /**
     *
     * @throws QueryException
     */
    public function export(Event $event, \SplFileObject $toFile): void
    {
        $columns = [
            'id',
            'format',
            'typage',
            'speaker',
            'provenance',
            'theme',
            'langue',
            'titre',
            'description',
            'staff_notes',
            'programme_mentoring',
            'youtube_id',
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

    public function exportJoindIn(Event $event, \SplFileObject $toFile): void
    {
        // Récupération des données
        $talkAggregates = $this->talkRepository->getByEventWithSpeakers($event);

        $toFile->fputcsv(['Title','Description','Speaker','Date','Time','Type']);

        foreach ($talkAggregates as $talkAggregate) {

            // Gestion de la description
            $abstract = html_entity_decode($talkAggregate->talk->getAbstract(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $abstract = strip_tags($abstract);

            // Gestion des conférenciers
            $speakers = [];
            foreach ($talkAggregate->speakers as $speaker) {
                $speakers[] = $speaker->getFirstname() . ' ' . $speaker->getLastname();
            }
            if ($speakers === []) {
                $speakers[] = '-';
            }
            $speakers = implode(',', $speakers);

            // Gestion du type de conférence
            if ($talkAggregate->planning?->getIsKeynote()) {
                $type = 'Keynote';
            } elseif (Talk::TYPE_WORKSHOP === $talkAggregate->talk->getType()) {
                $type = 'Workshop';
            } else {
                $type = 'Talk';
            }

            $toFile->fputcsv([
                $talkAggregate->talk->getTitle(),
                $abstract,
                $speakers,
                $talkAggregate->planning?->getStart()?->format('Y-m-d'),
                $talkAggregate->planning?->getStart()?->format('H:i'),
                $type,
            ]);
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

        foreach ($talksWithSpeakers as $talkWithSpeaker) {
            yield $this->prepareLine($talkWithSpeaker['talk'], $talkWithSpeaker['.aggregation']['speaker']);
        }
    }

    /**
     *
     * @throws \Exception
     */
    private function prepareLine(Talk $talk, array $speakers): array
    {
        return [
            'id' => $talk->getId(),
            'format' => $talk->getTypeLabel(),
            'typage' => '',
            'speaker' => $this->prepareSpeakersLabel($speakers),
            'provenance' => $this->prepareSpeakersLocalities($speakers),
            'theme' => '',
            'langue' => $this->getLanguageLabel($talk),
            'titre' => $talk->getTitle(),
            'description' => $talk->getAbstract(),
            'staff_notes' => $talk->getStaffNotes(),
            'programme_mentoring' => $talk->getNeedsMentoring() ? 'oui' : 'non',
            'youtube_id' => $talk->getYoutubeId(),
        ];
    }

    private function prepareSpeakersLabel(array $speakers): string
    {
        return implode(',', $this->getSpeakersLabels($speakers));
    }

    private function getSpeakersLabels(array $speakers): array
    {
        $names = [];
        foreach ($speakers as $speaker) {
            $names[] = $speaker->getLabel();
        }

        return $names;
    }

    /**
     * @param array|Speaker[] $speakers
     */
    private function prepareSpeakersLocalities(array $speakers): string
    {
        return implode(',', $this->getSpeakersLocalities($speakers));
    }

    /**
     * @param array|Speaker[] $speakers
     */
    private function getSpeakersLocalities(array $speakers): array
    {
        $localities = [];
        foreach ($speakers as $speaker) {
            $localities[] = $speaker->getLocality();
        }

        return $localities;
    }

    /**
     * @return string
     */
    private function getLanguageLabel(Talk $talk)
    {
        try {
            return $talk->getLanguageLabel();
        } catch (\Exception) {
            return '';
        }
    }
}
