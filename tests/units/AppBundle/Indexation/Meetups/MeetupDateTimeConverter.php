<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups\tests\units;

use AppBundle\Indexation\Meetups\MeetupDateTimeConverter as TestedClass;
use DateTimeZone;
use Exception;

class MeetupDateTimeConverter extends \atoum
{
    /**
     * @dataProvider validDateStringProvider
     *
     * @param string $dateString
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $seconds
     * @param string $timezoneName
     * @throws Exception
     */
    public function testConvertValidStringToDateTime($dateString, $year, $month, $day, $hour, $minute, $seconds, $timezoneName): void
    {
        $meetupDateTimeConverter = new TestedClass();

        $timezoneName = $meetupDateTimeConverter->getTimezoneFromAbbreviation($timezoneName)->getName();
        $timezone = new DateTimeZone($timezoneName);

        $this
            ->given($meetupDateTimeConverter)
            ->then
            ->dateTime($meetupDateTimeConverter->convertStringToDateTime($dateString))
            ->hasDate($year, $month, $day)
            ->hasTime($hour, $minute, $seconds)
            ->hasTimeZone($timezone);
    }

    /**
     * Data provider for valid date strings and their expected DateTime objects.
     *
     * @return array
     */
    public function validDateStringProvider()
    {
        return [
            ['jeu. 03 janv. 2022, 02:30 UTC+1', 2022, 01, 03, 02, 30, 0, 'UTC+1'],
            ['lun. 01 févr. 2022, 18:30 UTC+2', 2022, 02, 01, 18, 30, 0, 'UTC+2'],
            ['lun. 01 fevr. 2022, 18:30 UTC+2', 2022, 02, 01, 18, 30, 0, 'UTC+2'],
            ['ven. 04 mars 2022, 18:30 UTC+1', 2022, 03, 04, 18, 30, 0, 'UTC+1'],
            ['dim. 02 avr. 2020, 18:30 UTC+1', 2020, 04, 02, 18, 30, 0, 'UTC+1'],
            ['mar. 02 mai 2022, 18:30 UTC', 2022, 05, 02, 18, 30, 0, 'UTC'],
            ['ven. 03 juin 2021, 18:30 UTC+1', 2021, 06, 03, 18, 30, 0, 'UTC+1'],
            ['dim. 03 juil. 2022, 18:30 UTC+2', 2022, 07, 03, 18, 30, 0, 'UTC+2'],
            ['mer. 02 août 2022, 11:30 UTC+1', 2022, 8, 02, 11, 30, 0, 'UTC+1'],
            ['mer. 02 aout. 2022, 11:30 UTC+1', 2022, 8, 02, 11, 30, 0, 'UTC+1'],
            ['sam. 03 sept. 2022, 18:30 UTC', 2022, 9, 03, 18, 30, 0, 'UTC'],
            ['lun. 03 oct. 2025, 18:30 UTC+1', 2025, 10, 03, 18, 30, 0, 'UTC+1'],
            ['jeu. 03 nov. 2022, 16:30 UTC+2', 2022, 11, 03, 16, 30, 0, 'UTC+2'],
            ['sam. 03 déc. 2019, 18:30 UTC+1', 2019, 12, 03, 18, 30, 0, 'UTC+1'],
        ];
    }

    /**
     * @dataProvider invalidDateStringProvider
     *
     * @param string $invalidDateString
     */
    public function testConvertInvalidStringToDateTime($invalidDateString): void
    {
        $this
            ->given($meetupDateTimeConverter = new TestedClass())
            ->exception(function () use ($meetupDateTimeConverter, $invalidDateString): void {
                $meetupDateTimeConverter->convertStringToDateTime($invalidDateString);
            })
            ->isInstanceOf(\Exception::class); // Remplacez par le type de l'exception personnalisée si nécessaire
    }

    /**
     * Data provider for invalid date strings.
     *
     * @return array
     */
    public function invalidDateStringProvider()
    {
        return [
            ['invalid_date_format'],
            ['sam. 03 déc. 2019, 18:30 UTC+100'],
            ['sam. 31 fev. 2019, 18:30 UTC+1'],
            ['mar. 31 dec. 2021, 24:00 CET'],
            ['jeu. 15 juil. 2020, 10:30 InvalidTimeZone'],
            ['dim. 20 sep. 2025, 12:45'],
            ['ven. 18 dece. 2023, 15:45 UTC',],
        ];
    }
}
