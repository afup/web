<?php

namespace AppBundle\Offices;

class OfficesCollection
{
    public function getAll()
    {
        return [
            'bordeaux' => [
                'label' => 'Bordeaux',
                'latitude' => '44.837912',
                'longitude' => '-0.579541',
                'meetup_urlname' => 'Bordeaux-PHP-Meetup',
                'meetup_id' => '18197674',
                'logo_url' => '/images/offices/bordeaux.jpg',
            ],
            'limoges' => [
                'label' => 'Limoges',
                'latitude' => '45.85',
                'longitude' => '1.25',
                'meetup_urlname' => 'afup-limoges-php',
                'meetup_id' => '23162834',
                'logo_url' => '/images/offices/limoges.jpg',
            ],
            'lille' => [
                'label' => 'Hauts de France',
                'latitude' => '50.637222',
                'longitude' => '3.063333',
                'meetup_urlname' => 'afup-hauts-de-france-php',
                'meetip_id' => '23840677',
                'logo_url' => '/images/offices/hdf.jpg',
            ],
            'luxembourg' => [
                'label' => 'Luxembourg',
                'latitude' => '49.61',
                'longitude' => '6.13333',
                'meetup_urlname' => 'afup-luxembourg-php',
                'meetup_id' => '19631843',
                'logo_url' => '/images/offices/luxembourg.jpg',
            ],
            'lyon' => [
                'label' => 'Lyon',
                'latitude' => '45.759723',
                'longitude' => '4.842223',
                'meetup_urlname' => 'afup-lyon-php',
                'meetup_id' => '19630036',
                'logo_url' => '/images/offices/lyon.png',
            ],
            'marseille' => [
                'label' => 'Marseille',
                'latitude' => '43.296346',
                'longitude' => '5.36988923',
                'meetup_urlname' => 'Marseille-PHP-User-Group',
                'meetup_id' => '18152912',
                'logo_url' => '/images/offices/marseille.jpg',

            ],
            'montpellier' => [
                'label' => 'Montpellier',
                'latitude' => '43.611944',
                'longitude' => '3.877222',
                'meetup_urlname' => 'montpellier-php-Meetup',
                'meetup_id' => '18724486',
                'logo_url' => '/images/offices/montpellier.jpg',
            ],
            'nantes' => [
                'label' => 'Nantes',
                'latitude' => '47.21806',
                'longitude' => '-1.55278',
                'meetup_urlname' => 'afup-nantes-php',
                'meetup_id' => '23839991',
                'logo_url' => '/images/offices/afup-icon-color.svg',
            ],
            'paris' => [
                'label' => 'Paris',
                'latitude' => '48.856578',
                'longitude' => '2.351828',
                'meetup_urlname' => 'afup-nantes-php',
                'meetup_id' => '23839991',
                'logo_url' => '/images/offices/paris.png',
            ],
            'poitiers' => [
                'label' => 'Poitiers',
                'latitude' => '46.581945',
                'longitude' => '0.336112',
                'meetup_urlname' => 'afup-poitiers-php',
                'meetup_id' => '23106095',
                'logo_url' => '/images/offices/afup-icon-color.svg',
            ],
            'reims' => [
                'label' => 'Reims',
                'latitude' => '49.26278',
                'longitude' => '4.03472',
                'meetup_urlname' => 'afup-reims-php',
                'meetup_id' => '23255694',
                'logo_url' => '/images/offices/reims.jpg',
            ],
            'rennes' => [
                'label' => 'Rennes',
                'latitude' => '48.114722',
                'longitude' => '-1.679444',
                'meetup_urlname' => 'AFUP-Rennes',
                'meetup_id' => '22364687',
                'logo_url' => '/images/offices/rennes.jpg',
            ],
            'toulouse' => [
                'label' => 'Toulouse',
                'latitude' => '43.604482',
                'longitude' => '1.443962',
                'meetup_urlname' => 'AperoPHP-Toulouse',
                'meetup_id' => '19947513',
                'logo_url' => '/images/offices/toulouse.jpg',
            ],
            'valence' => [
                'label' => 'Valence',
                'latitude' => '44.933333',
                'longitude' => '4.891667',
                'logo_url' => '/images/offices/afup-icon-color.svg',
            ],
            'clermont' => [
                'label' => 'Clermont',
                'latitude' => '45.786781',
                'longitude' => '3.115074',
                'logo_url' => '/images/offices/afup-icon-color.svg',
            ],
        ];
    }

    public function findByMeetupId($meetupId)
    {
        foreach ($this->getAll() as $office) {
            if (!isset($office['meetup_id']) || $office['meetup_id'] != $meetupId) {
                continue;
            }

            return $office;
        }

        throw new \InvalidArgumentException('Office nout found');
    }
}
