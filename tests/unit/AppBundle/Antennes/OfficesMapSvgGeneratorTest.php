<?php

declare(strict_types=1);

namespace AppBundle\Tests\Antennes;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Ville;
use AppBundle\Antennes\FranceMap;
use AppBundle\Antennes\AncrageLegende;
use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Meetup;
use AppBundle\Antennes\OfficesMapSvgGenerator;
use AppBundle\Antennes\Point;
use AppBundle\Antennes\Position;
use AppBundle\Antennes\ReseauxSociaux;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OfficesMapSvgGeneratorTest extends TestCase
{
    private const string ANTENNE_COLOR = '#2b3e74';

    public function testEmptyMapOnlyContainsTheBackground(): void
    {
        $svg = $this->generate([]);

        self::assertCount(count(FranceMap::AREAS), $svg->query('//svg:path[@data-id]'));
        self::assertCount(0, $svg->query('//svg:path[@fill]'));
        self::assertCount(0, $svg->query('//svg:a | //svg:circle | //svg:image | //svg:text | //svg:title'));
    }

    public function testAntenneIsDrawnOnTheMap(): void
    {
        $svg = $this->generate([
            $this->antenne(
                code: 'bordeaux',
                libelle: 'Bordeaux',
                departements: ['33', '40'],
                pays: ['lux'],
                reseauxSociaux: new ReseauxSociaux(blog: 'https://bordeaux.afup.org'),
            ),
        ]);

        foreach (['department-33', 'department-40', 'country-lux'] as $area) {
            $path = $this->area($svg, $area);
            self::assertSame(self::ANTENNE_COLOR, $path->getAttribute('fill'));

            $link = $path->parentNode;
            self::assertInstanceOf(\DOMElement::class, $link);
            self::assertSame('a', $link->localName);
            self::assertSame('https://bordeaux.afup.org', $link->getAttribute('href'));
            self::assertSame('Bordeaux', $link->getAttribute('aria-label'));
        }
        self::assertSame('', $this->area($svg, 'department-75')->getAttribute('fill'));

        // Coordonnées calculées par l'ancienne carte Mapael pour Bordeaux.
        $circle = $this->single($svg, '//svg:circle');
        self::assertSame('193.86', $circle->getAttribute('cx'));
        self::assertSame('413.02', $circle->getAttribute('cy'));

        self::assertSame(
            ['M330,440L270,500', 'M270,500L230,500'],
            $this->attributes($svg, '//svg:path[@stroke="#000"]', 'd'),
        );

        self::assertSame('/images/offices/bordeaux.svg', $this->single($svg, '//svg:image')->getAttribute('href'));

        $text = $this->single($svg, '//svg:a/svg:text');
        self::assertSame('Bordeaux', $text->textContent);
        $parent = $text->parentNode;
        self::assertInstanceOf(\DOMElement::class, $parent);
        self::assertSame('https://bordeaux.afup.org', $parent->getAttribute('href'));
    }

    public function testSecondCityHasItsOwnPointAndLines(): void
    {
        $svg = $this->generate([
            $this->antenne(secondeVille: new Ville(
                new Point(630, 140),
                new Point(680, 200),
                new Point(720, 200),
                new Position(49.1193, 6.1757),
            )),
        ]);

        self::assertCount(2, $svg->query('//svg:circle'));
        self::assertSame(
            ['M330,440L270,500', 'M270,500L230,500', 'M630,140L680,200', 'M680,200L720,200'],
            $this->attributes($svg, '//svg:path[@stroke="#000"]', 'd'),
        );
        // Une seule légende par antenne, même avec deux villes.
        self::assertCount(1, $svg->query('//svg:image'));
    }

    public function testAddingAndRemovingAnAntenne(): void
    {
        $bordeaux = $this->antenne(code: 'bordeaux', libelle: 'Bordeaux', departements: ['33']);
        $lyon = $this->antenne(
            code: 'lyon',
            libelle: 'Lyon',
            departements: ['69'],
            urlLogo: '/images/offices/lyon.svg',
            position: new Position(45.759723, 4.842223),
        );

        $withLyon = $this->generate([$bordeaux, $lyon]);
        self::assertSame(self::ANTENNE_COLOR, $this->area($withLyon, 'department-69')->getAttribute('fill'));
        self::assertCount(2, $withLyon->query('//svg:circle'));
        self::assertSame(['Bordeaux', 'Lyon'], $this->texts($withLyon));
        self::assertSame(
            ['/images/offices/bordeaux.svg', '/images/offices/lyon.svg'],
            $this->attributes($withLyon, '//svg:image', 'href'),
        );

        $withoutLyon = $this->generate([$bordeaux]);
        self::assertSame('', $this->area($withoutLyon, 'department-69')->getAttribute('fill'));
        self::assertCount(1, $withoutLyon->query('//svg:circle'));
        self::assertSame(['Bordeaux'], $this->texts($withoutLyon));
        self::assertSame(['/images/offices/bordeaux.svg'], $this->attributes($withoutLyon, '//svg:image', 'href'));
    }

    /**
     * Positions reprises de l'ancienne carte Mapael (troisième point en 230,500).
     */
    #[DataProvider('legendAttachments')]
    public function testLegendPosition(
        AncrageLegende $ancrage,
        string $logoX,
        string $logoY,
        string $textX,
        string $textY,
        string $textAnchor,
    ): void {
        $svg = $this->generate([$this->antenne(ancrageLegende: $ancrage)]);

        $image = $this->single($svg, '//svg:image');
        self::assertSame([$logoX, $logoY], [$image->getAttribute('x'), $image->getAttribute('y')]);

        $text = $this->single($svg, '//svg:text');
        self::assertSame(
            [$textX, $textY, $textAnchor],
            [$text->getAttribute('x'), $text->getAttribute('y'), $text->getAttribute('text-anchor')],
        );
    }

    /**
     * @return iterable<string, array{AncrageLegende, string, string, string, string, string}>
     */
    public static function legendAttachments(): iterable
    {
        yield 'left' => [AncrageLegende::Gauche, '234', '475', '287', '498', 'start'];
        yield 'right' => [AncrageLegende::Droite, '181', '475', '173', '498', 'end'];
        yield 'top' => [AncrageLegende::Haut, '155', '504', '208', '527', 'start'];
        yield 'bottom' => [AncrageLegende::Bas, '155', '446', '208', '469', 'start'];
    }

    #[DataProvider('links')]
    public function testLinkIsTheFirstSocialNetworkOfTheAntenne(ReseauxSociaux $reseauxSociaux, ?Meetup $meetup, ?string $expected): void
    {
        $svg = $this->generate([$this->antenne(reseauxSociaux: $reseauxSociaux, meetup: $meetup)]);

        self::assertSame(self::ANTENNE_COLOR, $this->area($svg, 'department-33')->getAttribute('fill'));

        if ($expected === null) {
            self::assertCount(0, $svg->query('//svg:a'));

            return;
        }

        self::assertSame([$expected, $expected], $this->attributes($svg, '//svg:a', 'href'));
    }

    /**
     * @return iterable<string, array{ReseauxSociaux, ?Meetup, ?string}>
     */
    public static function links(): iterable
    {
        yield 'blog' => [new ReseauxSociaux(youtube: 'https://youtube.com/@afup', blog: 'https://blog.example', twitter: 'afup'), null, 'https://blog.example'];
        yield 'twitter' => [new ReseauxSociaux(twitter: 'afup_reims', linkedin: 'afup'), null, 'https://twitter.com/afup_reims'];
        yield 'linkedin' => [new ReseauxSociaux(linkedin: 'afup-hdf'), null, 'https://www.linkedin.com/company/afup-hdf'];
        yield 'bluesky' => [new ReseauxSociaux(bluesky: 'hdf.afup.org'), new Meetup('afup', '1'), 'https://bsky.app/profile/hdf.afup.org'];
        yield 'meetup' => [new ReseauxSociaux(youtube: 'https://youtube.com/@afup'), new Meetup('afup-lyon-php', '1'), 'https://www.meetup.com/fr-FR/afup-lyon-php'];
        yield 'youtube' => [new ReseauxSociaux(youtube: 'https://youtube.com/@afup'), null, 'https://youtube.com/@afup'];
        yield 'aucun' => [new ReseauxSociaux(), null, null];
    }

    public function testAntenneWithoutMapOnlyColorsItsAreas(): void
    {
        $svg = $this->generate([$this->antenne(withMap: false)]);

        self::assertSame(self::ANTENNE_COLOR, $this->area($svg, 'department-33')->getAttribute('fill'));
        self::assertCount(0, $svg->query('//svg:circle | //svg:image | //svg:text'));
    }

    public function testLabelsAreEscaped(): void
    {
        $svg = $this->generate([$this->antenne(libelle: 'Aix & <Marseille>')]);

        self::assertSame('Aix & <Marseille>', $this->single($svg, '//svg:text')->textContent);
    }

    public function testCorsicaUsesItsOwnProjection(): void
    {
        // Ajaccio
        self::assertEqualsWithDelta(['x' => 562.72, 'y' => 589.03], FranceMap::project(41.9192, 8.7386), 0.01);
    }

    /**
     * @param list<Antenne> $antennes
     */
    private function generate(array $antennes): \DOMXPath
    {
        $document = new \DOMDocument();
        self::assertTrue($document->loadXML(new OfficesMapSvgGenerator()->generate($antennes)));

        $xpath = new \DOMXPath($document);
        $xpath->registerNamespace('svg', 'http://www.w3.org/2000/svg');

        return $xpath;
    }

    private function area(\DOMXPath $svg, string $key): \DOMElement
    {
        return $this->single($svg, sprintf('//svg:path[@data-id="%s"]', $key));
    }

    private function single(\DOMXPath $svg, string $query): \DOMElement
    {
        $nodes = $svg->query($query);
        self::assertNotFalse($nodes);
        self::assertCount(1, $nodes);

        $node = $nodes->item(0);
        self::assertInstanceOf(\DOMElement::class, $node);

        return $node;
    }

    /**
     * @return list<string>
     */
    private function attributes(\DOMXPath $svg, string $query, string $attribute): array
    {
        $values = [];
        foreach ($svg->query($query) ?: [] as $node) {
            self::assertInstanceOf(\DOMElement::class, $node);
            $values[] = $node->getAttribute($attribute);
        }

        return $values;
    }

    /**
     * @return list<string>
     */
    private function texts(\DOMXPath $svg): array
    {
        $texts = [];
        foreach ($svg->query('//svg:text') ?: [] as $node) {
            $texts[] = $node->textContent;
        }

        return $texts;
    }

    /**
     * @param list<string>|null $departements
     * @param list<string>|null $pays
     */
    private function antenne(
        string $code = 'bordeaux',
        string $libelle = 'Bordeaux',
        ?array $departements = ['33'],
        ?array $pays = null,
        string $urlLogo = '/images/offices/bordeaux.svg',
        ?ReseauxSociaux $reseauxSociaux = null,
        ?Meetup $meetup = null,
        bool $withMap = true,
        AncrageLegende $ancrageLegende = AncrageLegende::Droite,
        Position $position = new Position(44.837912, -0.579541),
        ?Ville $secondeVille = null,
    ): Antenne {
        return new Antenne(
            code: $code,
            libelle: $libelle,
            meetup: $meetup,
            urlLogo: $urlLogo,
            reseauxSociaux: $reseauxSociaux ?? new ReseauxSociaux(blog: 'https://' . $code . '.afup.org'),
            carte: $withMap ? new Carte(
                utiliserSecondeCouleur: false,
                ancrageLegende: $ancrageLegende,
                premiereVille: new Ville(new Point(330, 440), new Point(270, 500), new Point(230, 500), $position),
                secondeVille: $secondeVille,
            ) : null,
            departements: $departements,
            pays: $pays,
        );
    }
}
