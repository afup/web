<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

final class AntenneRepository
{
    /** @var array<string, Antenne> */
    private array $antennes;

    public function __construct()
    {
        $this->makeAntennes();
    }

    private function add(Antenne $antenne): void
    {
        $this->antennes[$antenne->code] = $antenne;
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAll(): array
    {
        return $this->antennes;
    }

    /**
     * @return array<string, Antenne>
     */
    public function getAllSortedByLabels(): array
    {
        $antennes = array_filter(
            $this->getAll(),
            fn(Antenne $antenne): bool => !$antenne->hideOnOfficesPage,
        );

        uasort(
            $antennes,
            fn(Antenne $a, Antenne $b): int => strcmp($a->label, $b->label),
        );

        return $antennes;
    }

    public function findByCode(string $code): Antenne
    {
        if (!array_key_exists($code, $this->antennes)) {
            throw new \InvalidArgumentException("Antenne introuvable via le code $code");
        }

        return $this->antennes[$code];
    }

    /**
     * @return array<string, string>
     */
    public function getOrderedLabelsByKey(): array
    {
        $labels = [];
        foreach ($this->getAllSortedByLabels() as $antenne) {
            $labels[$antenne->code] = $antenne->label;
        }

        return $labels;
    }

    private function makeAntennes(): void
    {
        $this->add(new Antenne(
            'bordeaux',
            'Bordeaux',
            new Meetup('bordeaux-php-meetup', '18197674'),
            '/images/offices/bordeaux.svg',
            new Socials(
                twitter: 'AFUP_Bordeaux',
                linkedin: 'afup-bordeaux',
                bluesky: 'bordeaux.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Right,
                new City(
                    new Point(330, 440),
                    new Point(270, 500),
                    new Point(230, 500),
                    new Position(44.837912, -0.579541),
                ),
            ),
            ['33'],
        ));

        $this->add(new Antenne(
            'limoges',
            'Limoges',
            new Meetup('afup-limoges-php', '23162834'),
            '/images/offices/limoges.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCPYMUpcC3b5zd-hVNGEWHAA',
                blog: 'https://limoges.afup.org',
                twitter: 'AFUP_Limoges',
                linkedin: 'afup-limoges',
            ),
            new Map(
                false,
                LegendAttachment::Right,
                new City(
                    new Point(410, 380),
                    new Point(320, 380),
                    new Point(230, 430),
                    new Position(45.85, 1.25),
                ),
            ),
            ['87'],
        ));

        $this->add(new Antenne(
            'lille',
            'Hauts de France',
            new Meetup('afup-hauts-de-france-php', '23840677'),
            '/images/offices/hdf.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCkMGtNcB-VeqMlQ9p2JMIKg',
                twitter: 'afup_hdf',
                linkedin: 'afup-hdf',
                bluesky: 'hdf.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Left,
                new City(
                    new Point(490, 55),
                    new Point(530, 30),
                    new Point(605, 20),
                    new Position(50.637222, 3.063333),
                ),
                new City(
                    new Point(490, 55),
                    new Point(530, 30),
                    new Point(460, 110),
                    new Position(49.894054, 2.295847),
                ),
            ),
            ['59', '80'],
        ));

        $this->add(new Antenne(
            'luxembourg',
            'Luxembourg',
            new Meetup('afup-luxembourg-php', '19631843'),
            '/images/offices/luxembourg.svg',
            new Socials(
                blog: 'https://luxembourg.afup.org',
                twitter: 'afup_luxembourg',
            ),
            new Map(
                false,
                LegendAttachment::Left,
                new City(
                    new Point(630, 130),
                    new Point(660, 140),
                    new Point(717, 140),
                    new Position(49.61, 6.13333),
                ),
            ),
            null,
            ['lux'],
        ));

        $this->add(new Antenne(
            'lyon',
            'Lyon',
            new Meetup('afup-lyon-php', '19630036'),
            '/images/offices/lyon.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCSHpe_EYwK0ZhitIJPGSjlQ',
                blog: 'https://lyon.afup.org',
                twitter: 'AFUP_Lyon',
                linkedin: 'afup-lyon',
                bluesky: 'lyon.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Left,
                new City(
                    new Point(570, 380),
                    new Point(680, 320),
                    new Point(710, 320),
                    new Position(45.759723, 4.842223),
                ),
            ),
            ['69'],
        ));

        $this->add(new Antenne(
            'marseille',
            'Aix-Marseille',
            new Meetup('afup-aix-marseille-php', '18152912'),
            '/images/offices/marseille.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UC77cQ1izl155u6Y8daMZYiA',
                blog: 'https://aix-marseille.afup.org',
                twitter: 'AFUP_AixMrs',
            ),
            new Map(
                false,
                LegendAttachment::Top,
                new City(
                    new Point(600, 540),
                    new Point(600, 600),
                    new Point(600, 600),
                    new Position(43.296346, 5.36988923),
                ),
            ),
            ['13'],
        ));

        $this->add(new Antenne(
            'montpellier',
            'Montpellier',
            new Meetup('montpellier-php-meetup', '18724486'),
            '/images/offices/montpellier.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCr9f4-DksVhdv45q2245HeQ',
                twitter: 'afup_mtp',
                linkedin: 'montpellier-afup',
                bluesky: 'montpellier.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Top,
                new City(
                    new Point(530, 520),
                    new Point(470, 590),
                    new Point(470, 670),
                    new Position(43.611944, 3.877222),
                ),
            ),
            ['34'],
        ));

        $this->add(new Antenne(
            'nantes',
            'Nantes',
            new Meetup('afup-nantes-php', '23839991'),
            '/images/offices/nantes.svg',
            new Socials(
                blog: 'https://nantes.afup.org',
                twitter: 'afup_nantes',
                linkedin: 'afup-nantes',
            ),
            new Map(
                true,
                LegendAttachment::Right,
                new City(
                    new Point(285, 290),
                    new Point(180, 290),
                    new Point(180, 290),
                    new Position(47.21806, -1.55278),
                ),
            ),
            ['44'],
        ));

        $this->add(new Antenne(
            'paris',
            'Paris',
            new Meetup('afup-paris-php', '19629965'),
            '/images/offices/paris.svg',
            new Socials(
                twitter: 'afup_paris',
                bluesky: 'paris.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Right,
                new City(
                    new Point(460, 180),
                    new Point(400, 60),
                    new Point(360, 60),
                    new Position(48.856578, 2.351828),
                ),
            ),
            ['75', '77', '78', '91', '92', '93', '94', '95'],
        ));

        $this->add(new Antenne(
            'poitiers',
            'Poitiers',
            new Meetup('afup-poitiers-php', '23106095'),
            '/images/offices/poitiers.svg',
            new Socials(
                twitter: 'afup_poitiers',
                linkedin: 'afup-poitiers',
                bluesky: 'poitiers.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Right,
                new City(
                    new Point(365, 330),
                    new Point(285, 360),
                    new Point(180, 360),
                    new Position(46.581945, 0.336112),
                ),
            ),
            ['86'],
        ));

        $this->add(new Antenne(
            'reims',
            'Reims',
            new Meetup('afup-reims-php', '23255694'),
            '/images/offices/reims.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCmkMmVqrt7eI7YMZovew_xw',
                twitter: 'afup_reims',
            ),
            new Map(
                false,
                LegendAttachment::Left,
                new City(
                    new Point(540, 150),
                    new Point(600, 70),
                    new Point(650, 70),
                    new Position(49.26278, 4.03472),
                ),
            ),
            ['51'],
        ));

        $this->add(new Antenne(
            'rennes',
            'Rennes',
            new Meetup('afup-rennes', '22364687'),
            '/images/offices/rennes.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCv1VGfqKhygjTOZkdVUWfpQ',
                blog: 'https://rennes.afup.org',
                twitter: 'AFUP_Rennes',
                linkedin: 'afup-rennes',
            ),
            new Map(
                false,
                LegendAttachment::Bottom,
                new City(
                    new Point(285, 220),
                    new Point(150, 220),
                    new Point(120, 170),
                    new Position(48.114722, -1.679444),
                ),
            ),
            ['35'],
        ));

        $this->add(new Antenne(
            'toulouse',
            'Toulouse',
            new Meetup('aperophp-toulouse', '19947513'),
            '/images/offices/toulouse.svg',
            new Socials(
                blog: 'https://toulouse.afup.org',
                twitter: 'afup_toulouse',
                linkedin: 'https://www.linkedin.com/company/afup-toulouse/',
            ),
            new Map(
                false,
                LegendAttachment::Top,
                new City(
                    new Point(420, 520),
                    new Point(290, 590),
                    new Point(290, 600),
                    new Position(43.604482, 1.443962),
                ),
            ),
            ['31'],
        ));

        $this->add(new Antenne(
            'lorraine',
            'Lorraine',
            new Meetup('afup-lorraine-php', '26854931'),
            '/images/offices/lorraine.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UC08QRZncvlgWxUbVbmUs42Q',
                twitter: 'AFUP_Lorraine',
                linkedin: 'afup-lorraine',
                bluesky: 'lorraine.afup.org',
            ),
            new Map(
                false,
                LegendAttachment::Left,
                new City(
                    new Point(650, 160),
                    new Point(700, 220),
                    new Point(740, 220),
                    new Position(49.0685, 6.6151),
                ),
            ),
            ['54', '55', '57', '88'],
        ));

        $this->add(new Antenne(
            'clermont',
            'Clermont',
            null,
            '/images/offices/empty.svg',
            new Socials(),
            null,
            null,
            null,
            true,
        ));

        $this->add(new Antenne(
            'tours',
            'Tours',
            new Meetup('afup-tours-php', '28638984'),
            '/images/offices/tours.svg',
            new Socials(
                youtube: 'https://www.youtube.com/channel/UCtKhGIofgKM9ecrdZNyn_pA',
                blog: 'https://tours.afup.org',
                twitter: 'AFUP_Tours',
                linkedin: 'afup-tours',
            ),
            new Map(
                false,
                LegendAttachment::Right,
                new City(
                    new Point(380, 270),
                    new Point(240, 90),
                    new Point(200, 90),
                    new Position(47.380001068115234, 0.6899999976158142),
                ),
            ),
            [''],
        ));
    }
}
