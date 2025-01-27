<?php

declare(strict_types=1);

namespace AppBundle\Offices;

class OfficesCollection
{
    public function getAll(): array
    {
        return [
            'bordeaux' => [
                'label' => 'Bordeaux',
                'latitude' => '44.837912',
                'longitude' => '-0.579541',
                'meetup_urlname' => 'bordeaux-php-meetup',
                'meetup_id' => '18197674',
                'logo_url' => '/images/offices/bordeaux.svg',
                'twitter' => 'AFUP_Bordeaux',
                'linkedin' => 'afup-bordeaux',
                'bluesky' => 'bordeaux.afup.org',
                'map' => [
                    "legend-first-point-x" => "330",
                    "legend-first-point-y" => "440",
                    "legend-second-point-x" => "270",
                    "legend-second-point-y" => "500",
                    "legend-third-point-x" => "230",
                    "legend-third-point-y" => "500",
                    "legend-attachment" => "right",
                    "point-latitude" => "44.837912",
                    "point-longitude" => "-0.579541",
                ],
                'departements' => ['33'],
            ],
            'limoges' => [
                'label' => 'Limoges',
                'latitude' => '45.85',
                'longitude' => '1.25',
                'meetup_urlname' => 'afup-limoges-php',
                'meetup_id' => '23162834',
                'logo_url' => '/images/offices/limoges.svg',
                'twitter' => 'AFUP_Limoges',
                'youtube' => 'https://www.youtube.com/channel/UCPYMUpcC3b5zd-hVNGEWHAA',
                'blog_url' => 'http://limoges.afup.org/',
                'linkedin' => 'afup-limoges',
                'map' => [
                    "legend-first-point-x" => "410",
                    "legend-first-point-y" => "380",
                    "legend-second-point-x" => "320",
                    "legend-second-point-y" => "380",
                    "legend-third-point-x" => "230",
                    "legend-third-point-y" => "430",
                    "legend-attachment" => "right",
                    "point-latitude" => "45.85",
                    "point-longitude" => "1.25",
                ],
                'departements' => ['87'],
            ],
            'lille' => [
                'label' => 'Hauts de France',
                'latitude' => '50.637222',
                'longitude' => '3.063333',
                'meetup_urlname' => 'afup-hauts-de-france-php',
                'meetup_id' => '23840677',
                'logo_url' => '/images/offices/hdf.svg',
                'twitter' => 'afup_hdf',
                'youtube' => 'https://www.youtube.com/channel/UCkMGtNcB-VeqMlQ9p2JMIKg',
                'linkedin' => 'afup-hdf',
                'bluesky' => 'hdf.afup.org',
                'map' => [
                    "legend-first-point-x" => "490",
                    "legend-first-point-y" => "55",
                    "legend-second-point-x" => "530",
                    "legend-second-point-y" => "30",
                    "legend-third-point-x" => "605",
                    "legend-third-point-y" => "20",
                    "legend-attachment" => "left",
                    "point-latitude" => "50.637222",
                    "point-longitude" => "3.063333",
                    "legend-second-city-first-point-x" => "490",
                    "legend-second-city-first-point-y" => "55",
                    "legend-second-city-second-point-x" => "530",
                    "legend-second-city-second-point-y" => "30",
                    "legend-second-city-third-point-x" => "460",
                    "legend-second-city-third-point-y" => "110",
                    "point-second-city-latitude" => "49.894054",
                    "point-second-city-longitude" => "2.295847",
                ],
                'departements' => ['59', '80'],
            ],
            'luxembourg' => [
                'label' => 'Luxembourg',
                'latitude' => '49.61',
                'longitude' => '6.13333',
                'meetup_urlname' => 'afup-luxembourg-php',
                'meetup_id' => '19631843',
                'logo_url' => '/images/offices/luxembourg.svg',
                'twitter' => 'afup_luxembourg',
                'blog_url' => 'http://luxembourg.afup.org/',
                'map' => [
                    "legend-first-point-x" => "630",
                    "legend-first-point-y" => "130",
                    "legend-second-point-x" => "660",
                    "legend-second-point-y" => "140",
                    "legend-third-point-x" => "717",
                    "legend-third-point-y" => "140",
                    "legend-attachment" => "left",
                    "point-latitude" => "49.61",
                    "point-longitude" => "6.13333",
                ],
                'pays' => ['lux'],
            ],
            'lyon' => [
                'label' => 'Lyon',
                'latitude' => '45.759723',
                'longitude' => '4.842223',
                'meetup_urlname' => 'afup-lyon-php',
                'meetup_id' => '19630036',
                'logo_url' => '/images/offices/lyon.svg',
                'youtube' => 'https://www.youtube.com/channel/UCSHpe_EYwK0ZhitIJPGSjlQ',
                'twitter' => 'AFUP_Lyon',
                'blog_url' => 'http://lyon.afup.org',
                'linkedin' => 'afup-lyon',
                'bluesky' => 'lyon.afup.org',
                'map' => [
                    "legend-first-point-x" => "570",
                    "legend-first-point-y" => "380",
                    "legend-second-point-x" => "680",
                    "legend-second-point-y" => "320",
                    "legend-third-point-x" => "710",
                    "legend-third-point-y" => "320",
                    "legend-attachment" => "left",
                    "point-latitude" => "45.759723",
                    "point-longitude" => "4.842223",
                ],
                'departements' => ['69'],
            ],
            'marseille' => [
                'label' => 'Aix-Marseille',
                'latitude' => '43.296346',
                'longitude' => '5.36988923',
                'meetup_urlname' => 'afup-aix-marseille-php',
                'meetup_id' => '18152912',
                'logo_url' => '/images/offices/marseille.svg',
                'twitter' => 'AFUP_AixMrs',
                'youtube' => 'https://www.youtube.com/channel/UC77cQ1izl155u6Y8daMZYiA',
                'blog_url' => 'http://aix-marseille.afup.org/',
                'map' => [
                    "legend-first-point-x" => "600",
                    "legend-first-point-y" => "540",
                    "legend-second-point-x" => "600",
                    "legend-second-point-y" => "600",
                    "legend-third-point-x" => "600",
                    "legend-third-point-y" => "600",
                    "legend-attachment" => "top",
                    "point-latitude" => "43.296346",
                    "point-longitude" => "5.36988923",
                ],
                'departements' => ['13'],
            ],
            'montpellier' => [
                'label' => 'Montpellier',
                'latitude' => '43.611944',
                'longitude' => '3.877222',
                'meetup_urlname' => 'montpellier-php-meetup',
                'meetup_id' => '18724486',
                'logo_url' => '/images/offices/montpellier.svg',
                'twitter' => 'afup_mtp',
                'youtube' => 'https://www.youtube.com/channel/UCr9f4-DksVhdv45q2245HeQ',
                'bluesky' => 'montpellier.afup.org',
                'linkedin' => 'montpellier-afup',
                'map' => [
                    "legend-first-point-x" => "530",
                    "legend-first-point-y" => "520",
                    "legend-second-point-x" => "470",
                    "legend-second-point-y" => "590",
                    "legend-third-point-x" => "470",
                    "legend-third-point-y" => "670",
                    "legend-attachment" => "top",
                    "point-latitude" => "43.611944",
                    "point-longitude" => "3.877222",
                ],
                'departements' => ['34'],
            ],
            'nantes' => [
                'label' => 'Nantes',
                'latitude' => '47.21806',
                'longitude' => '-1.55278',
                'meetup_urlname' => 'afup-nantes-php',
                'meetup_id' => '23839991',
                'logo_url' => '/images/offices/nantes.svg',
                'twitter' => 'afup_nantes',
                'blog_url' => 'http://nantes.afup.org/',
                'linkedin' => 'afup-nantes',
                'bluesky' => 'nantes.afup.org',
                'map' => [
                    "legend-first-point-x" => "285",
                    "legend-first-point-y" => "290",
                    "legend-second-point-x" => "180",
                    "legend-second-point-y" => "290",
                    "legend-third-point-x" => "180",
                    "legend-third-point-y" => "290",
                    "legend-attachment" => "right",
                    "point-latitude" => "47.21806",
                    "point-longitude" => "-1.55278",
                ],
                'map_use_second_color' => true,
                'departements' => ['44'],
            ],
            'paris' => [
                'label' => 'Paris',
                'latitude' => '48.856578',
                'longitude' => '2.351828',
                'meetup_urlname' => 'afup-paris-php',
                'meetup_id' => '19629965',
                'logo_url' => '/images/offices/paris.svg',
                'twitter' => 'afup_paris',
                'bluesky' => 'paris.afup.org',
                'map' => [
                    "legend-first-point-x" => "460",
                    "legend-first-point-y" => "180",
                    "legend-second-point-x" => "400",
                    "legend-second-point-y" => "60",
                    "legend-third-point-x" => "360",
                    "legend-third-point-y" => "60",
                    "legend-attachment" => "right",
                    "point-latitude" => "48.856578",
                    "point-longitude" => "2.351828",
                ],
                'departements' => ['75', '77', '78', '91', '92', '93', '94', '95'],
            ],
            'poitiers' => [
                'label' => 'Poitiers',
                'latitude' => '46.581945',
                'longitude' => '0.336112',
                'meetup_urlname' => 'afup-poitiers-php',
                'meetup_id' => '23106095',
                'logo_url' => '/images/offices/poitiers.svg',
                'twitter' => 'afup_poitiers',
                'linkedin' => 'afup-poitiers',
                'bluesky' => 'poitiers.afup.org',
                'map' => [
                    "legend-first-point-x" => "365",
                    "legend-first-point-y" => "330",
                    "legend-second-point-x" => "285",
                    "legend-second-point-y" => "360",
                    "legend-third-point-x" => "180",
                    "legend-third-point-y" => "360",
                    "legend-attachment" => "right",
                    "point-latitude" => "46.581945",
                    "point-longitude" => "0.336112",
                ],
                'departements' => ['86'],
            ],
            'reims' => [
                'label' => 'Reims',
                'latitude' => '49.26278',
                'longitude' => '4.03472',
                'meetup_urlname' => 'afup-reims-php',
                'meetup_id' => '23255694',
                'logo_url' => '/images/offices/reims.svg',
                'twitter' => 'afup_reims',
                'youtube' => 'https://www.youtube.com/channel/UCmkMmVqrt7eI7YMZovew_xw',
                'map' => [
                    "legend-first-point-x" => "540",
                    "legend-first-point-y" => "150",
                    "legend-second-point-x" => "600",
                    "legend-second-point-y" => "70",
                    "legend-third-point-x" => "650",
                    "legend-third-point-y" => "70",
                    "legend-attachment" => "left",
                    "point-latitude" => "49.26278",
                    "point-longitude" => "4.03472",
                ],
                'departements' => ['51'],
            ],
            'rennes' => [
                'label' => 'Rennes',
                'latitude' => '48.114722',
                'longitude' => '-1.679444',
                'meetup_urlname' => 'afup-rennes',
                'meetup_id' => '22364687',
                'logo_url' => '/images/offices/rennes.svg',
                'twitter' => 'AFUP_Rennes',
                'blog_url' => 'https://rennes.afup.org/',
                'youtube' => 'https://www.youtube.com/channel/UCv1VGfqKhygjTOZkdVUWfpQ',
                'linkedin' => 'afup-rennes',
                'map' => [
                    "legend-first-point-x" => "285",
                    "legend-first-point-y" => "220",
                    "legend-second-point-x" => "150",
                    "legend-second-point-y" => "220",
                    "legend-third-point-x" => "120",
                    "legend-third-point-y" => "170",
                    "legend-attachment" => "bottom",
                    "point-latitude" => "48.114722",
                    "point-longitude" => "-1.679444",
                ],
                'departements' => ['35'],
            ],
            'toulouse' => [
                'label' => 'Toulouse',
                'latitude' => '43.604482',
                'longitude' => '1.443962',
                'meetup_urlname' => 'aperophp-toulouse',
                'meetup_id' => '19947513',
                'logo_url' => '/images/offices/toulouse.svg',
                'twitter' => 'afup_toulouse',
                'blog_url' => 'http://toulouse.afup.org/',
                'map' => [
                    "legend-first-point-x" => "420",
                    "legend-first-point-y" => "520",
                    "legend-second-point-x" => "290",
                    "legend-second-point-y" => "590",
                    "legend-third-point-x" => "290",
                    "legend-third-point-y" => "600",
                    "legend-attachment" => "top",
                    "point-latitude" => "43.604482",
                    "point-longitude" => "1.443962",
                ],
                'departements' => ['31'],
            ],
            'lorraine' => [
                'label' => 'Lorraine',
                'latitude' => '49.0685',
                'longitude' => '6.6151',
                'logo_url' => '/images/offices/lorraine.svg',
                'twitter' => 'AFUP_Lorraine',
                'youtube' => 'https://www.youtube.com/channel/UC08QRZncvlgWxUbVbmUs42Q',
                'departements' => ['54', '55', '57', '88'],
                'meetup_urlname' => 'afup-lorraine-php',
                'meetup_id' => '26854931',
                'bluesky' => 'lorraine.afup.org',
                'linkedin' => 'afup-lorraine',
                'map' => [
                    "legend-first-point-x" => "650",
                    "legend-first-point-y" => "160",
                    "legend-second-point-x" => "700",
                    "legend-second-point-y" => "220",
                    "legend-third-point-x" => "740",
                    "legend-third-point-y" => "220",
                    "legend-attachment" => "left",
                    "point-latitude" => "49.0685",
                    "point-longitude" => "6.6151",
                ]
            ],
            'clermont' => [
                'label' => 'Clermont',
                'latitude' => '45.786781',
                'longitude' => '3.115074',
                'logo_url' => '/images/offices/empty.svg',
                'hide_on_offices_page' => true,
            ],
            'tours' => [
                'label' => 'Tours',
                'latitude' => '47.380001068115234',
                'longitude' => '0.6899999976158142',
                'logo_url' => '/images/offices/tours.svg',
                'twitter' => 'AFUP_Tours',
                'blog_url' => 'http://tours.afup.org',
                'departements' => ['37'],
                'meetup_urlname' => 'afup-tours-php',
                'meetup_id' => '28638984',
                'youtube' => 'https://www.youtube.com/channel/UCtKhGIofgKM9ecrdZNyn_pA',
                'linkedin' => 'afup-tours',
                'map' => [
                    "legend-first-point-x" => "380",
                    "legend-first-point-y" => "270",
                    "legend-second-point-x" => "240",
                    "legend-second-point-y" => "90",
                    "legend-third-point-x" => "200",
                    "legend-third-point-y" => "90",
                    "legend-attachment" => "right",
                    "point-latitude" => "47.380001068115234",
                    "point-longitude" => "0.6899999976158142",
                ],
            ]
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

    public function getAllSortedByLabels(): array
    {
        $offices = $this->getAll();

        uasort(
            $offices,
            fn ($a, $b): int => strcmp($a['label'], $b['label'])
        );

        return array_filter(
            $offices,
            function ($value): bool {
                if (!isset($value['hide_on_offices_page'])) {
                    return true;
                }

                return !$value['hide_on_offices_page'];
            }
        );
    }

    public function findByCode($code)
    {
        $all = $this->getAll();

        if (!isset($all[$code])) {
            throw new \InvalidArgumentException('Office not found');
        }

        return $all[$code];
    }

    /**
     * @return mixed[]
     */
    public function getOrderedLabelsByKey(): array
    {
        $labels = [];
        foreach ($this->getAllSortedByLabels() as $key => $office) {
            $labels[$key] = $office['label'];
        }

        return $labels;
    }
}
