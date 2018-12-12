<?php

namespace AppBundle\Event\Speaker;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Speaker;
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
     * @param Event $event
     *
     * @return \Generator
     *
     * @throws \CCMBenchmark\Ting\Query\QueryException
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

    /**
     * @param Speaker $speaker
     * @param array $talks
     *
     * @return array
     *
     */
    private function prepareLine(Speaker $speaker, array $talks)
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
