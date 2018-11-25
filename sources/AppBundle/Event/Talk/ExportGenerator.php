<?php

namespace AppBundle\Event\Talk;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Talk;

class ExportGenerator
{
    /**
     * @var TalkRepository
     */
    private $talkRepository;

    /**
     * @param TalkRepository $talkRepository
     */
    public function __construct(TalkRepository $talkRepository)
    {
        $this->talkRepository = $talkRepository;
    }

    /**
     * @param Event $event
     * @param \SplFileObject $toFile
     *
     * @throws \CCMBenchmark\Ting\Query\QueryException
     */
    public function export(Event $event, \SplFileObject $toFile)
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
     * @param Event $event
     *
     * @return \Generator
     *
     * @throws \CCMBenchmark\Ting\Query\QueryException
     */
    private function getFromRegistrationsOnEvent(Event $event)
    {
        $talksWithSpeakers = $this->talkRepository->getAllByEventWithSpeakers($event);

        foreach ($talksWithSpeakers as $talkWithSpeaker) {
            yield $this->prepareLine($talkWithSpeaker['talk'], $talkWithSpeaker['.aggregation']['speaker']);
        }
    }

    /**
     * @param Talk $talk
     * @param array $speakers
     *
     * @return array
     *
     * @throws \Exception
     */
    private function prepareLine(Talk $talk, array $speakers)
    {
        return [
            'id' => $talk->getId(),
            'format' => $talk->getTypeLabel(),
            'typage' => '',
            'speaker' => $this->prepareSpeakersLabel($speakers),
            'provenance' => '',
            'theme' => '',
            'langue' => $this->getLanguageLabel($talk),
            'titre' => $talk->getTitle(),
            'description' => $talk->getAbstract(),
            'staff_notes' => $talk->getStaffNotes(),
            'programme_mentoring' => $talk->getNeedsMentoring() ? 'oui' : 'non',
        ];
    }

    /**
     * @param array $speakers
     *
     * @return string
     */
    private function prepareSpeakersLabel(array $speakers)
    {
        return implode(',', $this->getSpeakersLabels($speakers));
    }

    /**
     * @param array $speakers
     *
     * @return array
     */
    private function getSpeakersLabels(array $speakers)
    {
        $names = [];
        foreach ($speakers as $speaker) {
            $names[] = $speaker->getLabel();
        }

        return $names;
    }

    /**
     * @param Talk $talk
     *
     * @return string
     */
    private function getLanguageLabel(Talk $talk)
    {
        try {
            return $talk->getLanguageLabel();
        } catch (\Exception $e) {
            return '';
        }
    }
}
