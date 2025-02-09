<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use DateTime;
use DateTimeZone;
use Exception;
use Normalizer;

class MeetupDateTimeConverter
{
    /**
     * @param string $dateTimeString
     *
     *
     * @throws Exception
     */
    public function convertStringToDateTime($dateTimeString): \DateTime
    {
        $unAccentDateString = $this->getDateTimeStringWithoutAccents($dateTimeString);

        $parsedDateTime = $this->parseDateString($unAccentDateString);
        $this->validateTime($parsedDateTime['time']);

        $monthNames = [
            'janv' => 1, 'fevr' => 2, 'mars' => 3, 'avr' => 4, 'mai' => 5, 'juin' => 6,
            'juil' => 7, 'aout' => 8, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        $monthAbbreviation = $parsedDateTime['monthAbbreviation'];
        if (!array_key_exists($monthAbbreviation, $monthNames)) {
            throw new Exception('Abbréviation de mois invalide : ' . $monthAbbreviation);
        }

        [$year, $day, $hour, $minute, $timezoneAbbreviation] = [
            $parsedDateTime['year'],
            $parsedDateTime['day'],
            $parsedDateTime['time'][0],
            $parsedDateTime['time'][1],
            $parsedDateTime['timezoneAbbreviation'],
        ];

        $dateTimeString = sprintf(
            '%04d-%02d-%02d %02d:%02d',
            $year,
            $monthNames[$monthAbbreviation],
            $day,
            $hour,
            $minute
        );

        $timezone = $this->getTimezoneFromAbbreviation($timezoneAbbreviation);

        try {
            return new DateTime($dateTimeString, $timezone);
        } catch (Exception $e) {
            throw new Exception('Format de date invalide', $e->getCode(), $e);
        }
    }

    /**
     *
     * @throws Exception
     */
    private function parseDateString(string $dateTimeString): array
    {
        $pattern = '/(\S+)\s+(\d+)\s+(\S+)\s+(\d+),\s+(\d+:\d+)\s+([A-Za-z0-9\s\+\-]+)/u';
        preg_match($pattern, $dateTimeString, $matches);

        if (count($matches) !== 7) {
            throw new Exception('Format de date invalide : ' . $dateTimeString);
        }

        return [
            'day' => intval($matches[2]),
            'monthAbbreviation' => mb_strtolower(str_replace('.', '', $matches[3]), 'UTF-8'),
            'year' => intval($matches[4]),
            'time' => explode(':', $matches[5]),
            'timezoneAbbreviation' => $matches[6],
        ];
    }

    /**
     * @param int $time
     *
     *
     * @throws Exception
     */
    private function validateTime($time): void
    {
        [$hour, $minute] = $time;

        if ($hour >= 24 || $minute >= 60) {
            throw new Exception('Heure invalide : ' . implode(':', $time));
        }
    }

    /**
     *
     *
     * @throws Exception
     */
    public function getTimezoneFromAbbreviation(string $timezoneAbbreviation): \DateTimeZone
    {
        $timezoneNameMap = $this->getTimezoneNameMap();

        if (isset($timezoneNameMap[$timezoneAbbreviation])) {
            return new DateTimeZone($timezoneNameMap[$timezoneAbbreviation]);
        }

        throw new Exception('Fuseau horaire inconnu : ' . $timezoneAbbreviation);
    }

    /**
     * Mapping des abréviations de fuseau horaire aux noms complets des fuseaux horaires.
     *
     * Cela permet de gérer les conversions entre les heures d'été (CEST) et l'heure standard (CET)
     * pour les événements en France.
     *
     * @return string[]
     */
    private function getTimezoneNameMap(): array
    {
        return [
            'UTC+0' => 'UTC',
            'UTC' => 'UTC',
            'GMT' => 'GMT',
            'UTC+1' => 'Europe/Paris', // CET (Central European Time)
            'CET' => 'Europe/Paris',   // CET (Central European Time)
            'UTC+2' => 'Europe/Athens', // CEST (Central European Summer Time)
            'CEST' => 'Europe/Athens',  // CEST (Central European Summer Time)
        ];
    }

    /**
     * @param string $dateTimeString
     * @return string
     */
    private function getDateTimeStringWithoutAccents($dateTimeString): ?string
    {
        $normalized = Normalizer::normalize($dateTimeString, Normalizer::FORM_KD);

        return preg_replace('/\p{Mn}/u', '', $normalized);
    }
}
