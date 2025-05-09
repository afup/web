<?php

declare(strict_types=1);

namespace AppBundle\Ting;

use CCMBenchmark\Ting\Serializer\DateTime;

class DateTimeWithTimeZoneSerializer extends DateTime
{
    public function unserialize($serialized, array $options = [])
    {
        $value = parent::unserialize($serialized, $options);

        if ($value instanceof \DateTime && isset($options['timezone'])) {
            $timeZone = $options['timezone'];
        } else {
            $timeZone = date_default_timezone_get();
        }

        if ($value && $timeZone) {
            $value->setTimezone(new \DateTimeZone($timeZone));
        }

        return $value;
    }
}
