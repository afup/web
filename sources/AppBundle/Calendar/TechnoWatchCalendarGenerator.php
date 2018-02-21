<?php

namespace AppBundle\Calendar;

use Sabre\VObject\Component\VCalendar;

class TechnoWatchCalendarGenerator
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $currentDate;

    /**
     * @param string $name
     * @param \DateTime $currentDate
     */
    public function __construct($name, \DateTime $currentDate)
    {
        $this->name = $name;
        $this->currentDate = $currentDate;
    }

    /**
     * @param string $googleSpreadsheetCsvUrl
     * @param $displayPrefix
     * @param null $filter
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
     * @param null $filter
     *
     * @return array
     */
    private function prepareEvents($googleSpreadsheetCsvUrl, $filter = null)
    {
        $url = $googleSpreadsheetCsvUrl;

        $fp = fopen($url, 'rb');
        if (!$fp) {
            throw new \RuntimeException("Error opening spreadsheet");
        }

        $events = [];

        while (false !== ($row = fgetcsv($fp))) {
            if (0 === strlen(trim($row[0]))) {
                continue;
            }

            $date = \DateTimeImmutable::createFromFormat('d/m/Y', trim($row[0]));

            if (false == $date) {
                continue;
            }

            $date->setTime(0, 0, 0);

            if ($date < $this->currentDate) {
                continue;
            }

            $firstChair = trim($row[4]);
            $secondChair = trim($row[5]);

            if (strlen($filter) > 0 && (!($firstChair == $filter || $secondChair == $filter))) {
                continue;
            }

            if (0 === strlen($firstChair) || 0 == strlen($secondChair)) {
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
