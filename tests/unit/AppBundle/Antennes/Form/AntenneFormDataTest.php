<?php

declare(strict_types=1);

namespace AppBundle\Tests\Antennes\Form;

use AppBundle\Antennes\AncrageLegende;
use AppBundle\Antennes\Carte;
use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Form\AntenneFormData;
use AppBundle\Antennes\Form\CarteFormData;
use AppBundle\Antennes\Form\ReseauxSociauxFormData;
use AppBundle\Antennes\Form\VilleFormData;
use AppBundle\Antennes\Meetup;
use AppBundle\Antennes\Point;
use AppBundle\Antennes\Position;
use AppBundle\Antennes\ReseauxSociaux;
use AppBundle\Antennes\Ville;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AntenneFormDataTest extends TestCase
{
    public function testFromEntityPuisToEntityRestituentLAntenne(): void
    {
        $antenne = self::antenne(secondeVille: self::ville(46.0, 5.0));

        self::assertEquals($antenne, AntenneFormData::fromEntity($antenne)->toEntity());
    }

    public function testFromEntitySansCarteNiMeetup(): void
    {
        $antenne = new Antenne(
            code: 'clermont',
            libelle: 'Clermont',
            meetup: null,
            urlLogo: '/images/offices/clermont.svg',
            reseauxSociaux: new ReseauxSociaux(),
            carte: null,
            masqueePageAntennes: true,
        );

        $data = AntenneFormData::fromEntity($antenne);

        self::assertNull($data->meetupNomUrl);
        self::assertNull($data->meetupIdExterne);
        self::assertFalse($data->carte->afficherSurCarte);
        self::assertFalse($data->actif);
        self::assertEquals($antenne, $data->toEntity());
    }

    public function testActifEstLInverseDeMasqueePageAntennes(): void
    {
        $data = AntenneFormData::fromEntity(self::antenne());
        self::assertTrue($data->actif);

        $data->actif = false;
        self::assertTrue($data->toEntity()->masqueePageAntennes);

        $data->actif = true;
        self::assertFalse($data->toEntity()->masqueePageAntennes);
    }

    public function testAppliquerAModifieLAntenneSansToucherAuCode(): void
    {
        $antenne = self::antenne();
        $data = AntenneFormData::fromEntity($antenne);
        $data->code = 'autre-code';
        $data->libelle = 'Lyon et environs';
        $data->meetupNomUrl = '';
        $data->meetupIdExterne = '';
        $data->reseauxSociaux->linkedin = 'afup-lyon-officiel';
        $data->reseauxSociaux->blog = '';
        $data->carte->afficherSurCarte = false;
        $data->departements = ['69', '01'];
        $data->region = '';
        $data->actif = false;

        $data->appliquerA($antenne);

        self::assertSame('lyon', $antenne->code);
        self::assertSame('Lyon et environs', $antenne->libelle);
        self::assertNull($antenne->meetup);
        self::assertSame('afup-lyon-officiel', $antenne->reseauxSociaux->linkedin);
        self::assertNull($antenne->reseauxSociaux->blog);
        self::assertNull($antenne->carte);
        self::assertSame(['69', '01'], $antenne->departements);
        self::assertNull($antenne->region);
        self::assertTrue($antenne->masqueePageAntennes);
    }

    public function testMeetupIncompletEstIgnore(): void
    {
        $data = AntenneFormData::fromEntity(self::antenne());
        $data->meetupIdExterne = '  ';

        self::assertNull($data->toEntity()->meetup);
    }

    #[DataProvider('codes')]
    public function testValidationDuCode(string $code, bool $valide): void
    {
        $data = new AntenneFormData();
        $data->code = $code;

        $violations = self::validator()->validateProperty($data, 'code', [AntenneFormData::GROUPE_CREATION]);

        self::assertSame($valide, count($violations) === 0, (string) $violations);
    }

    /**
     * Le code sert d'alias GraphQL dans l'indexation des meetups.
     *
     * @return iterable<string, array{string, bool}>
     */
    public static function codes(): iterable
    {
        yield 'lettres' => ['lyon', true];
        yield 'underscore' => ['aix_marseille', true];
        yield 'chiffres' => ['lyon2', true];
        yield 'tiret' => ['aix-marseille', false];
        yield 'commence par un chiffre' => ['2lyon', false];
        yield 'commence par un underscore' => ['_lyon', false];
        yield 'majuscule' => ['Lyon', false];
    }

    #[DataProvider('meetups')]
    public function testValidationDuMeetup(string $propriete, string $valeur, bool $valide): void
    {
        $data = new AntenneFormData();
        $data->{$propriete} = $valeur;

        $violations = self::validator()->validateProperty($data, $propriete);

        self::assertSame($valide, count($violations) === 0, (string) $violations);
    }

    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function meetups(): iterable
    {
        yield 'id numérique' => ['meetupIdExterne', '19630036', true];
        yield 'id vide' => ['meetupIdExterne', '', true];
        yield 'id avec espaces' => ['meetupIdExterne', ' 19630036 ', true];
        yield 'id composé d\'espaces' => ['meetupIdExterne', '  ', true];
        yield 'id non numérique' => ['meetupIdExterne', 'afup-lyon', false];
        yield 'nom d\'URL' => ['meetupNomUrl', 'afup-lyon-php', true];
        yield 'nom d\'URL avec underscore' => ['meetupNomUrl', 'Afup_Lyon_PHP', true];
        yield 'nom d\'URL vide' => ['meetupNomUrl', '', true];
        yield 'URL complète' => ['meetupNomUrl', 'https://www.meetup.com/afup-lyon-php/', false];
    }

    public function testReseauxSociauxVidesDeviennentNull(): void
    {
        $data = new ReseauxSociauxFormData();
        $data->youtube = '';
        $data->blog = '  ';
        $data->twitter = 'AFUP_Lyon';

        self::assertEquals(new ReseauxSociaux(twitter: 'AFUP_Lyon'), $data->toReseauxSociaux());
    }

    public function testCarteNonAfficheeDonneNull(): void
    {
        $data = CarteFormData::fromCarte(self::carte());
        $data->afficherSurCarte = false;

        self::assertNull($data->toCarte());
    }

    public function testCarteAvecUneVille(): void
    {
        $data = CarteFormData::fromCarte(self::carte());

        self::assertEquals(self::carte(), $data->toCarte());
    }

    public function testCarteAvecUneSecondeVilleVideLIgnore(): void
    {
        $data = CarteFormData::fromCarte(self::carte());
        $data->secondeVille = new VilleFormData();

        self::assertEquals(self::carte(), $data->toCarte());
    }

    public function testCarteAvecDeuxVilles(): void
    {
        $carte = self::carte(self::ville(46.0, 5.0));

        self::assertEquals($carte, CarteFormData::fromCarte($carte)->toCarte());
    }

    public function testCompletudeDUneVille(): void
    {
        $ville = new VilleFormData();
        self::assertTrue($ville->estVide());
        self::assertFalse($ville->estComplete());

        $ville->latitude = 45.0;
        self::assertFalse($ville->estVide());
        self::assertFalse($ville->estComplete());

        $ville = VilleFormData::fromVille(self::ville(45.0, 4.0));
        self::assertFalse($ville->estVide());
        self::assertTrue($ville->estComplete());
    }

    private static function validator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
    }

    private static function antenne(?Ville $secondeVille = null): Antenne
    {
        return new Antenne(
            code: 'lyon',
            libelle: 'Lyon',
            meetup: new Meetup('afup-lyon-php', '19630036'),
            urlLogo: '/images/offices/lyon.svg',
            reseauxSociaux: new ReseauxSociaux(
                youtube: 'https://www.youtube.com/@afuplyon',
                blog: 'https://lyon.afup.org',
                twitter: 'AFUP_Lyon',
                linkedin: 'afup-lyon',
                bluesky: 'lyon.afup.org',
            ),
            carte: self::carte($secondeVille),
            departements: ['69'],
            pays: null,
            region: 'Auvergne-Rhône-Alpes',
        );
    }

    private static function carte(?Ville $secondeVille = null): Carte
    {
        return new Carte(
            utiliserSecondeCouleur: false,
            ancrageLegende: AncrageLegende::Gauche,
            premiereVille: self::ville(45.759723, 4.842223),
            secondeVille: $secondeVille,
        );
    }

    private static function ville(float $latitude, float $longitude): Ville
    {
        return new Ville(
            new Point(570, 380),
            new Point(680, 320),
            new Point(710, 320),
            new Position($latitude, $longitude),
        );
    }
}
