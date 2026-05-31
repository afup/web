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
            code: 'bordeaux',
            label: 'Bordeaux',
            meetup: new Meetup('bordeaux-php-meetup', '18197674'),
            logoUrl: '/images/offices/bordeaux.svg',
            socials: new Socials(
                blog: 'https://bordeaux.afup.org',
                twitter: 'AFUP_Bordeaux',
                linkedin: 'afup-bordeaux',
                bluesky: 'bordeaux.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Right,
                firstCity: new City(
                    new Point(330, 440),
                    new Point(270, 500),
                    new Point(230, 500),
                    new Position(44.837912, -0.579541),
                ),
            ),
            departments: ['33'],
        ));

        $this->add(new Antenne(
            code: 'limoges',
            label: 'Limoges',
            meetup: new Meetup('afup-limoges-php', '23162834'),
            logoUrl: '/images/offices/limoges.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuplimoges780',
                blog: 'https://limoges.afup.org',
                twitter: 'AFUP_Limoges',
                linkedin: 'afup-limoges',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Right,
                firstCity: new City(
                    new Point(410, 380),
                    new Point(320, 380),
                    new Point(230, 430),
                    new Position(45.85, 1.25),
                ),
            ),
            departments: ['87'],
        ));

        $this->add(new Antenne(
            code: 'lille',
            label: 'Hauts de France',
            meetup: new Meetup('afup-hauts-de-france-php', '23840677'),
            logoUrl: '/images/offices/hdf.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuphautsdefrance8344',
                blog: 'https://hdf.afup.org',
                twitter: 'afup_hdf',
                linkedin: 'afup-hdf',
                bluesky: 'hdf.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City(
                    new Point(490, 55),
                    new Point(530, 30),
                    new Point(605, 20),
                    new Position(50.637222, 3.063333),
                ),
                secondCity: new City(
                    new Point(490, 55),
                    new Point(530, 30),
                    new Point(460, 110),
                    new Position(49.894054, 2.295847),
                ),
            ),
            departments: ['59', '80'],
        ));

        $this->add(new Antenne(
            code: 'luxembourg',
            label: 'Luxembourg',
            meetup: new Meetup('afup-luxembourg-php', '19631843'),
            logoUrl: '/images/offices/luxembourg.svg',
            socials: new Socials(
                blog: 'https://luxembourg.afup.org',
                twitter: 'afup_luxembourg',
                linkedin: 'afup-luxembourg',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City(
                    new Point(630, 130),
                    new Point(660, 140),
                    new Point(717, 140),
                    new Position(49.61, 6.13333),
                ),
            ),
            pays: ['lux'],
        ));

        $this->add(new Antenne(
            code: 'lyon',
            label: 'Lyon',
            meetup: new Meetup('afup-lyon-php', '19630036'),
            logoUrl: '/images/offices/lyon.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuplyon',
                blog: 'https://lyon.afup.org',
                twitter: 'AFUP_Lyon',
                linkedin: 'afup-lyon',
                bluesky: 'lyon.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City(
                    new Point(570, 380),
                    new Point(680, 320),
                    new Point(710, 320),
                    new Position(45.759723, 4.842223),
                ),
            ),
            departments: ['69'],
        ));

        $this->add(new Antenne(
            code: 'marseille',
            label: 'Aix-Marseille',
            meetup: new Meetup('afup-aix-marseille-php', '18152912'),
            logoUrl: '/images/offices/marseille.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@AFUPAixMarseille',
                blog: 'https://aix-marseille.afup.org',
                twitter: 'AFUP_AixMrs',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Top,
                firstCity: new City(
                    new Point(600, 540),
                    new Point(600, 600),
                    new Point(600, 600),
                    new Position(43.296346, 5.36988923),
                ),
            ),
            departments: ['13'],
        ));

        $this->add(new Antenne(
            code: 'montpellier',
            label: 'Montpellier',
            meetup: new Meetup('montpellier-php-meetup', '18724486'),
            logoUrl: '/images/offices/montpellier.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afupmontpellier6846',
                blog: 'https://montpellier.afup.org',
                twitter: 'afup_mtp',
                linkedin: 'montpellier-afup',
                bluesky: 'montpellier.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Top,
                firstCity: new City(
                    new Point(530, 520),
                    new Point(470, 590),
                    new Point(470, 670),
                    new Position(43.611944, 3.877222),
                ),
            ),
            departments: ['34'],
        ));

        $this->add(new Antenne(
            code: 'nantes',
            label: 'Nantes',
            meetup: new Meetup('afup-nantes-php', '23839991'),
            logoUrl: '/images/offices/nantes.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@AFUPNantes',
                blog: 'https://nantes.afup.org',
                twitter: 'afup_nantes',
                linkedin: 'afup-nantes',
                bluesky: 'nantes.afup.org',
            ),
            map: new Map(
                useSecondColor: true,
                legendAttachment: LegendAttachment::Right,
                firstCity: new City(
                    new Point(285, 290),
                    new Point(180, 290),
                    new Point(180, 290),
                    new Position(47.21806, -1.55278),
                ),
            ),
            departments: ['44'],
        ));

        $this->add(new Antenne(
            code: 'paris',
            label: 'Paris',
            meetup: new Meetup('afup-paris-php', '19629965'),
            logoUrl: '/images/offices/paris.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afupparis',
                blog: 'https://paris.afup.org',
                twitter: 'afup_paris',
                linkedin: 'afup-paris',
                bluesky: 'paris.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Right,
                firstCity: new City(
                    new Point(460, 180),
                    new Point(400, 60),
                    new Point(360, 60),
                    new Position(48.856578, 2.351828),
                ),
            ),
            departments: ['75', '77', '78', '91', '92', '93', '94', '95'],
        ));

        $this->add(new Antenne(
            code: 'poitiers',
            label: 'Poitiers',
            meetup: new Meetup('afup-poitiers-php', '23106095'),
            logoUrl: '/images/offices/poitiers.svg',
            socials: new Socials(
                blog: 'https://poitiers.afup.org',
                twitter: 'afup_poitiers',
                linkedin: 'afup-poitiers',
                bluesky: 'poitiers.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Right,
                firstCity: new City(
                    new Point(365, 330),
                    new Point(285, 360),
                    new Point(180, 360),
                    new Position(46.581945, 0.336112),
                ),
            ),
            departments: ['86'],
        ));

        $this->add(new Antenne(
            code: 'reims',
            label: 'Reims',
            meetup: new Meetup('afup-reims-php', '23255694'),
            logoUrl: '/images/offices/reims.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afupreims5347',
                twitter: 'afup_reims',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City(
                    new Point(540, 150),
                    new Point(600, 70),
                    new Point(650, 70),
                    new Position(49.26278, 4.03472),
                ),
            ),
            departments: ['51'],
        ));

        $this->add(new Antenne(
            code: 'rennes',
            label: 'Rennes',
            meetup: new Meetup('afup-rennes', '22364687'),
            logoUrl: '/images/offices/rennes.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuprennes7058',
                blog: 'https://rennes.afup.org',
                twitter: 'AFUP_Rennes',
                linkedin: 'afup-rennes',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Bottom,
                firstCity: new City(
                    new Point(285, 220),
                    new Point(150, 220),
                    new Point(120, 170),
                    new Position(48.114722, -1.679444),
                ),
            ),
            departments: ['35'],
        ));

        $this->add(new Antenne(
            code: 'toulouse',
            label: 'Toulouse',
            meetup: new Meetup('aperophp-toulouse', '19947513'),
            logoUrl: '/images/offices/toulouse.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuptoulouse3302',
                blog: 'https://toulouse.afup.org',
                twitter: 'afup_toulouse',
                linkedin: 'afup-toulouse',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Top,
                firstCity: new City(
                    new Point(420, 520),
                    new Point(290, 590),
                    new Point(290, 600),
                    new Position(43.604482, 1.443962),
                ),
            ),
            departments: ['31'],
        ));

        $this->add(new Antenne(
            code: 'lorraine',
            label: 'Lorraine',
            meetup: new Meetup('afup-lorraine-php', '26854931'),
            logoUrl: '/images/offices/lorraine.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuplorraine',
                blog: 'https://lorraine.afup.org',
                twitter: 'AFUP_Lorraine',
                linkedin: 'afup-lorraine',
                bluesky: 'lorraine.afup.org',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City( // Nancy
                    new Point(630, 190),
                    new Point(680, 250),
                    new Point(720, 250),
                    new Position(48.6921, 6.1844),
                ),
                secondCity: new City( // Metz
                    new Point(630, 140),
                    new Point(680, 200),
                    new Point(720, 200),
                    new Position(49.1193, 6.1757),
                ),
            ),
            departments: ['54', '55', '57', '88'],
        ));

        $this->add(new Antenne(
            code: 'clermont',
            label: 'Clermont',
            meetup: null,
            logoUrl: '/images/offices/empty.svg',
            socials: new Socials(),
            map: null,
            hideOnOfficesPage: true,
        ));

        $this->add(new Antenne(
            code: 'tours',
            label: 'Tours',
            meetup: new Meetup('afup-tours-php', '28638984'),
            logoUrl: '/images/offices/tours.svg',
            socials: new Socials(
                youtube: 'https://www.youtube.com/@afuptours6345',
                blog: 'https://tours.afup.org',
                twitter: 'AFUP_Tours',
                linkedin: 'afup-tours',
            ),
            map: new Map(
                useSecondColor: false,
                legendAttachment: LegendAttachment::Left,
                firstCity: new City(
                    new Point(380, 270),
                    new Point(240, 90),
                    new Point(200, 90),
                    new Position(47.380001068115234, 0.6899999976158142),
                ),
            ),
            departments: ['37'],
        ));
    }
}
