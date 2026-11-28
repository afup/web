<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

use AppBundle\Doctrine\Type\UnixTimestampType;
use AppBundle\Event\Entity\Repository\PlanningRepository;
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
        private readonly PlanningRepository $planningRepository,
        private readonly SpeakerRepository $speakerRepository,
    ) {}

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

    public function exportJoindIn(Event $event, \SplFileObject $toFile): void
    {
        // Récupération des données
        $talkAggregates = $this->planningRepository->enrichTalkAggregates(
            $this->talkRepository->getByEventWithSpeakers($event),
        );

        $toFile->fputcsv(['Title','Description','Speaker','Date','Time','Type'], escape: '\\');

        $timezone = new \DateTimeZone(UnixTimestampType::TIMEZONE);

        foreach ($talkAggregates as $talkAggregate) {

            // Gestion de la description
            $abstract = html_entity_decode($talkAggregate->talk->getAbstract(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $abstract = strip_tags($abstract);

            // Gestion des conférenciers
            $speakers = [];
            foreach ($talkAggregate->speakers as $speaker) {
                $speakers[] = $speaker->firstname . ' ' . $speaker->lastname;
            }
            if ($speakers === []) {
                $speakers[] = '-';
            }
            $speakers = implode(',', $speakers);

            // Gestion des horaires : stockés en timestamp, ils sont exportés dans la
            // timezone de l'événement et non dans celle du serveur.
            $start = $talkAggregate->planning?->start;
            $start = $start === null ? null : \DateTimeImmutable::createFromInterface($start)->setTimezone($timezone);

            // Gestion du type de conférence
            if ($talkAggregate->planning?->isKeynote) {
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
                $start?->format('Y-m-d'),
                $start?->format('H:i'),
                $type,
            ], escape: '\\');
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

        foreach ($talks as $talk) {
            yield $this->prepareLine($talk, $speakersByTalk[(int) $talk->getId()] ?? []);
        }
    }

    /**
     * @param list<Speaker> $speakers
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

    /**
     * @param list<Speaker> $speakers
     */
    private function prepareSpeakersLabel(array $speakers): string
    {
        return implode(',', $this->getSpeakersLabels($speakers));
    }

    /**
     * @param list<Speaker> $speakers
     *
     * @return list<string>
     */
    private function getSpeakersLabels(array $speakers): array
    {
        $names = [];
        foreach ($speakers as $speaker) {
            $names[] = $speaker->label;
        }

        return $names;
    }

    /**
     * @param list<Speaker> $speakers
     */
    private function prepareSpeakersLocalities(array $speakers): string
    {
        return implode(',', $this->getSpeakersLocalities($speakers));
    }

    /**
     * @param list<Speaker> $speakers
     *
     * @return list<string>
     */
    private function getSpeakersLocalities(array $speakers): array
    {
        $localities = [];
        foreach ($speakers as $speaker) {
            $localities[] = (string) $speaker->locality;
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
