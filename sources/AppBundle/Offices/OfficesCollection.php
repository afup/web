<?php

namespace AppBundle\Offices;

class OfficesCollection
{
    public function getAll()
    {
        return [
            'bordeaux' => [
                'latitude' => '44.837912',
                'longitude' => '-0.579541'
            ],
            'limoges' => [
                'latitude' => '45.85',
                'longitude' => '1.25'
            ],
            'lille' => [
                'latitude' => '50.637222',
                'longitude' => '3.063333'
            ],
            'luxembourg' => [
                'latitude' => '49.61',
                'longitude' => '6.13333'
            ],
            'lyon' => [
                'latitude' => '45.759723',
                'longitude' => '4.842223'
            ],
            'marseille' => [
                'latitude' => '43.296346',
                'longitude' => '5.36988923'
            ],
            'montpellier' => [
                'latitude' => '43.611944',
                'longitude' => '3.877222'
            ],
            'nantes' => [
                'latitude' => '47.21806',
                'longitude' => '-1.55278'
            ],
            'paris' => [
                'latitude' => '48.856578',
                'longitude' => '2.351828'
            ],
            'poitiers' => [
                'latitude' => '46.581945',
                'longitude' => '0.336112'
            ],
            'reimes' => [
                'latitude' => '49.26278',
                'longitude' => '4.03472'
            ],
            'rennes' => [
                'latitude' => '48.114722',
                'longitude' => '-1.679444'
            ],
            'toulouse' => [
                'latitude' => '43.604482',
                'longitude' => '1.443962'
            ],
            'valence' => [
                'latitude' => '44.933333',
                'longitude' => '4.891667'
            ],
        ];
    }
}
