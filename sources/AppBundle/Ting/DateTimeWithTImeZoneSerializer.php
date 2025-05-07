<?php

namespace AppBundle\Ting;

use CCMBenchmark\Ting\Serializer\DateTime;

class DateTimeWithTImeZoneSerializer extends DateTime
{
    public function unserialize($serialized, array $options = [])
    {
        $value = parent::unserialize($serialized, $options);

        if ($value instanceof \DateTime && isset($options['timezone'])) {
            $value->setTimezone(new \DateTimeZone($options['timezone']));
        }

        return $value;
    }
}
