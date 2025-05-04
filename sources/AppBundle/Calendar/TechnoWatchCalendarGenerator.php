<?php

declare(strict_types=1);

namespace AppBundle\Calendar;

use Sabre\VObject\Component\VCalendar;

class TechnoWatchCalendarGenerator
{
    public function __construct(
        private readonly string $name,
        private readonly \DateTime $currentDate,
    ) {
    }

    /**
     * @param string $googleSpreadsheetCsvUrl
     * @param $displayPrefix
     *
     * @return string
     */
    public function generate($googleSpreadsheetCsvUrl, $displayPrefix, $filter = null)
    {
        $eventLabelPrefix = $displayPrefix ? $this->name . " : " : "";

        $vcalendar = new VCalendar();
        $vcalendar->add('X-WR-CALNAME', $this->name);
        foreach ($this->prepareEvents($googleSpreadsheetCsvUrl, $filter) as $event) {
            $vcalendar->add('VEVENT', [
                'SUMMARY' => $eventLabelPrefix . $event['first_chair'] . " / " . $event['second_chair'],
                'DTSTART;VALUE=DATE' => $event['date']->format('Ymd'),
                'DESCRIPTION' => '',
                'TRANSP' => 'TRANSPARENT',
            ]);
        }

        return $vcalendar->serialize();
    }

    /**
     * @param $googleSpreadsheetCsvUrl
     *
     */
    private function prepareEvents($googleSpreadsheetCsvUrl, $filter = null): array
    {
        $url = $googleSpreadsheetCsvUrl;

        $fp = fopen($url, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Error opening spreadsheet");
        }

        $events = [];

        while (false !== ($row = fgetcsv($fp))) {
            if (trim((string) $row[0]) === '') {
                continue;
            }

            $date = \DateTimeImmutable::createFromFormat('d/m/Y', trim((string) $row[0]));

            if (false == $date) {
                continue;
            }

            $date->setTime(0, 0, 0);

            if ($date < $this->currentDate) {
                continue;
            }

            $firstChair = trim((string) $row[4]);
            $secondChair = trim((string) $row[5]);

            if (strlen((string) $filter) > 0 && ($firstChair != $filter && $secondChair != $filter)) {
                continue;
            }

            if ($firstChair === '' || 0 == strlen($secondChair)) {
                continue;
            }

            $events[] = [
                'date' => $date,
                'first_chair' => $row[4],
                'second_chair' => $row[5],
            ];
        }

        return $events;
    }
}
