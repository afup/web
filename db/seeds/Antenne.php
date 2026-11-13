<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

/**
 * Antennes pour les environnements de dev / test.
 * Reprend à l'identique les données insérées par la migration 20261113100000_create_antenne_table.
 */
class Antenne extends AbstractSeed
{
    public function run(): void
    {
        $table = $this->table('afup_antenne');
        $table->truncate();

        $table
            ->insert(array_map(self::encodeRow(...), self::antennes()))
            ->save()
        ;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private static function encodeRow(array $row): array
    {
        $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;

        $row['masquee_page_antennes'] = (int) $row['masquee_page_antennes'];
        $row['departements'] = $row['departements'] === null ? null : json_encode($row['departements'], $flags);
        $row['pays'] = $row['pays'] === null ? null : json_encode($row['pays'], $flags);
        $row['reseaux_sociaux'] = json_encode($row['reseaux_sociaux'], $flags | JSON_FORCE_OBJECT);
        $row['carte'] = $row['carte'] === null ? null : json_encode($row['carte'], $flags);

        return $row;
    }

    /**
     * @param array{int, int} $premierPoint
     * @param array{int, int} $deuxiemePoint
     * @param array{int, int} $troisiemePoint
     * @return array<string, mixed>
     */
    private static function ville(array $premierPoint, array $deuxiemePoint, array $troisiemePoint, float $latitude, float $longitude): array
    {
        return [
            'premierPoint' => ['x' => $premierPoint[0], 'y' => $premierPoint[1]],
            'deuxiemePoint' => ['x' => $deuxiemePoint[0], 'y' => $deuxiemePoint[1]],
            'troisiemePoint' => ['x' => $troisiemePoint[0], 'y' => $troisiemePoint[1]],
            'position' => ['latitude' => $latitude, 'longitude' => $longitude],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function antennes(): array
    {
        return [
            [
                'code' => 'bordeaux',
                'libelle' => 'Bordeaux',
                'url_logo' => '/images/offices/bordeaux.svg',
                'masquee_page_antennes' => false,
                'region' => 'Nouvelle-Aquitaine',
                'departements' => ['33'],
                'pays' => null,
                'meetup_nom_url' => 'bordeaux-php-meetup',
                'meetup_id_externe' => '18197674',
                'reseaux_sociaux' => [
                    'blog' => 'https://bordeaux.afup.org',
                    'twitter' => 'AFUP_Bordeaux',
                    'linkedin' => 'afup-bordeaux',
                    'bluesky' => 'bordeaux.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'right',
                    'premiereVille' => self::ville([330, 440], [270, 500], [230, 500], 44.837912, -0.579541),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'limoges',
                'libelle' => 'Limoges',
                'url_logo' => '/images/offices/limoges.svg',
                'masquee_page_antennes' => false,
                'region' => 'Nouvelle-Aquitaine',
                'departements' => ['87'],
                'pays' => null,
                'meetup_nom_url' => 'afup-limoges-php',
                'meetup_id_externe' => '23162834',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuplimoges780',
                    'blog' => 'https://limoges.afup.org',
                    'twitter' => 'AFUP_Limoges',
                    'linkedin' => 'afup-limoges',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'right',
                    'premiereVille' => self::ville([410, 380], [320, 380], [230, 430], 45.85, 1.25),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'lille',
                'libelle' => 'Hauts de France',
                'url_logo' => '/images/offices/hdf.svg',
                'masquee_page_antennes' => false,
                'region' => 'Hauts-de-France',
                'departements' => ['59', '80'],
                'pays' => null,
                'meetup_nom_url' => 'afup-hauts-de-france-php',
                'meetup_id_externe' => '23840677',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuphautsdefrance8344',
                    'blog' => 'https://hdf.afup.org',
                    'twitter' => 'afup_hdf',
                    'linkedin' => 'afup-hdf',
                    'bluesky' => 'hdf.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([490, 55], [530, 30], [605, 20], 50.637222, 3.063333),
                    'secondeVille' => self::ville([490, 55], [530, 30], [460, 110], 49.894054, 2.295847),
                ],
            ],
            [
                'code' => 'luxembourg',
                'libelle' => 'Luxembourg',
                'url_logo' => '/images/offices/luxembourg.svg',
                'masquee_page_antennes' => false,
                'region' => 'Luxembourg',
                'departements' => null,
                'pays' => ['lux'],
                'meetup_nom_url' => 'afup-luxembourg-php',
                'meetup_id_externe' => '19631843',
                'reseaux_sociaux' => [
                    'blog' => 'https://luxembourg.afup.org',
                    'twitter' => 'afup_luxembourg',
                    'linkedin' => 'afup-luxembourg',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([630, 130], [660, 140], [717, 140], 49.61, 6.13333),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'lyon',
                'libelle' => 'Lyon',
                'url_logo' => '/images/offices/lyon.svg',
                'masquee_page_antennes' => false,
                'region' => 'Auvergne-Rhône-Alpes',
                'departements' => ['69'],
                'pays' => null,
                'meetup_nom_url' => 'afup-lyon-php',
                'meetup_id_externe' => '19630036',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuplyon',
                    'blog' => 'https://lyon.afup.org',
                    'twitter' => 'AFUP_Lyon',
                    'linkedin' => 'afup-lyon',
                    'bluesky' => 'lyon.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([570, 380], [680, 320], [710, 320], 45.759723, 4.842223),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'marseille',
                'libelle' => 'Aix-Marseille',
                'url_logo' => '/images/offices/marseille.svg',
                'masquee_page_antennes' => false,
                'region' => 'Provence-Alpes-Côte d\'Azur',
                'departements' => ['13'],
                'pays' => null,
                'meetup_nom_url' => 'afup-aix-marseille-php',
                'meetup_id_externe' => '18152912',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@AFUPAixMarseille',
                    'blog' => 'https://aix-marseille.afup.org',
                    'twitter' => 'AFUP_AixMrs',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'top',
                    'premiereVille' => self::ville([600, 540], [600, 600], [600, 600], 43.296346, 5.36988923),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'montpellier',
                'libelle' => 'Montpellier',
                'url_logo' => '/images/offices/montpellier.svg',
                'masquee_page_antennes' => false,
                'region' => 'Occitanie',
                'departements' => ['34'],
                'pays' => null,
                'meetup_nom_url' => 'montpellier-php-meetup',
                'meetup_id_externe' => '18724486',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afupmontpellier6846',
                    'blog' => 'https://montpellier.afup.org',
                    'twitter' => 'afup_mtp',
                    'linkedin' => 'montpellier-afup',
                    'bluesky' => 'montpellier.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'top',
                    'premiereVille' => self::ville([530, 520], [470, 590], [470, 670], 43.611944, 3.877222),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'nantes',
                'libelle' => 'Nantes',
                'url_logo' => '/images/offices/nantes.svg',
                'masquee_page_antennes' => false,
                'region' => 'Pays de la Loire',
                'departements' => ['44'],
                'pays' => null,
                'meetup_nom_url' => 'afup-nantes-php',
                'meetup_id_externe' => '23839991',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@AFUPNantes',
                    'blog' => 'https://nantes.afup.org',
                    'twitter' => 'afup_nantes',
                    'linkedin' => 'afup-nantes',
                    'bluesky' => 'nantes.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => true,
                    'ancrageLegende' => 'right',
                    'premiereVille' => self::ville([285, 290], [180, 290], [180, 290], 47.21806, -1.55278),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'paris',
                'libelle' => 'Paris',
                'url_logo' => '/images/offices/paris.svg',
                'masquee_page_antennes' => false,
                'region' => 'Île-de-France',
                'departements' => ['75', '77', '78', '91', '92', '93', '94', '95'],
                'pays' => null,
                'meetup_nom_url' => 'afup-paris-php',
                'meetup_id_externe' => '19629965',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afupparis',
                    'blog' => 'https://paris.afup.org',
                    'twitter' => 'afup_paris',
                    'linkedin' => 'afup-paris',
                    'bluesky' => 'paris.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'right',
                    'premiereVille' => self::ville([460, 180], [400, 60], [360, 60], 48.856578, 2.351828),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'poitiers',
                'libelle' => 'Poitiers',
                'url_logo' => '/images/offices/poitiers.svg',
                'masquee_page_antennes' => false,
                'region' => 'Nouvelle-Aquitaine',
                'departements' => ['86'],
                'pays' => null,
                'meetup_nom_url' => 'afup-poitiers-php',
                'meetup_id_externe' => '23106095',
                'reseaux_sociaux' => [
                    'blog' => 'https://poitiers.afup.org',
                    'twitter' => 'afup_poitiers',
                    'linkedin' => 'afup-poitiers',
                    'bluesky' => 'poitiers.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'right',
                    'premiereVille' => self::ville([365, 330], [285, 360], [180, 360], 46.581945, 0.336112),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'reims',
                'libelle' => 'Reims',
                'url_logo' => '/images/offices/reims.svg',
                'masquee_page_antennes' => false,
                'region' => 'Grand Est',
                'departements' => ['51'],
                'pays' => null,
                'meetup_nom_url' => 'afup-reims-php',
                'meetup_id_externe' => '23255694',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afupreims5347',
                    'twitter' => 'afup_reims',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([540, 150], [600, 70], [650, 70], 49.26278, 4.03472),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'rennes',
                'libelle' => 'Rennes',
                'url_logo' => '/images/offices/rennes.svg',
                'masquee_page_antennes' => false,
                'region' => 'Bretagne',
                'departements' => ['35'],
                'pays' => null,
                'meetup_nom_url' => 'afup-rennes',
                'meetup_id_externe' => '22364687',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuprennes7058',
                    'blog' => 'https://rennes.afup.org',
                    'twitter' => 'AFUP_Rennes',
                    'linkedin' => 'afup-rennes',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'bottom',
                    'premiereVille' => self::ville([285, 220], [150, 220], [120, 170], 48.114722, -1.679444),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'toulouse',
                'libelle' => 'Toulouse',
                'url_logo' => '/images/offices/toulouse.svg',
                'masquee_page_antennes' => false,
                'region' => 'Occitanie',
                'departements' => ['31'],
                'pays' => null,
                'meetup_nom_url' => 'aperophp-toulouse',
                'meetup_id_externe' => '19947513',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuptoulouse3302',
                    'blog' => 'https://toulouse.afup.org',
                    'twitter' => 'afup_toulouse',
                    'linkedin' => 'afup-toulouse',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'top',
                    'premiereVille' => self::ville([420, 520], [290, 590], [290, 600], 43.604482, 1.443962),
                    'secondeVille' => null,
                ],
            ],
            [
                'code' => 'lorraine',
                'libelle' => 'Lorraine',
                'url_logo' => '/images/offices/lorraine.svg',
                'masquee_page_antennes' => false,
                'region' => 'Grand Est',
                'departements' => ['54', '55', '57', '88'],
                'pays' => null,
                'meetup_nom_url' => 'afup-lorraine-php',
                'meetup_id_externe' => '26854931',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuplorraine',
                    'blog' => 'https://lorraine.afup.org',
                    'twitter' => 'AFUP_Lorraine',
                    'linkedin' => 'afup-lorraine',
                    'bluesky' => 'lorraine.afup.org',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([630, 190], [680, 250], [720, 250], 48.6921, 6.1844), // Nancy
                    'secondeVille' => self::ville([630, 140], [680, 200], [720, 200], 49.1193, 6.1757), // Metz
                ],
            ],
            [
                'code' => 'clermont',
                'libelle' => 'Clermont',
                'url_logo' => '/images/offices/empty.svg',
                'masquee_page_antennes' => true,
                'region' => null,
                'departements' => null,
                'pays' => null,
                'meetup_nom_url' => null,
                'meetup_id_externe' => null,
                'reseaux_sociaux' => [],
                'carte' => null,
            ],
            [
                'code' => 'tours',
                'libelle' => 'Tours',
                'url_logo' => '/images/offices/tours.svg',
                'masquee_page_antennes' => false,
                'region' => 'Centre-Val de Loire',
                'departements' => ['37'],
                'pays' => null,
                'meetup_nom_url' => 'afup-tours-php',
                'meetup_id_externe' => '28638984',
                'reseaux_sociaux' => [
                    'youtube' => 'https://www.youtube.com/@afuptours6345',
                    'blog' => 'https://tours.afup.org',
                    'twitter' => 'AFUP_Tours',
                    'linkedin' => 'afup-tours',
                ],
                'carte' => [
                    'utiliserSecondeCouleur' => false,
                    'ancrageLegende' => 'left',
                    'premiereVille' => self::ville([380, 270], [240, 90], [200, 90], 47.380001068115234, 0.6899999976158142),
                    'secondeVille' => null,
                ],
            ],
        ];
    }
}
