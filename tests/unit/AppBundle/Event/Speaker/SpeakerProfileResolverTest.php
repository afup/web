<?php

declare(strict_types=1);

namespace AppBundle\Tests\Event\Speaker;

use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Speaker\SpeakerIdentity;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Event\Speaker\SpeakerTalk;
use AppBundle\Event\Talk\PublishedEvent;
use AppBundle\Event\Talk\PublishedTalk;
use AppBundle\Event\Talk\PublishedTalkRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SpeakerProfileResolverTest extends TestCase
{
    #[DataProvider('labels')]
    public function testNormalizeLabel(string $label, string $expected): void
    {
        self::assertSame($expected, SpeakerProfileResolver::normalizeLabel($label));
    }

    public static function labels(): \Generator
    {
        yield 'accent' => ['Hélène SCHAPIRA', 'helene schapira'];
        yield 'sans accent' => ['Helene SCHAPIRA', 'helene schapira'];
        yield 'double espace et accent majuscule' => ['Benjamin  MOULÈS', 'benjamin moules'];
        yield 'espace final' => ['Cyrille GRANDVAL ', 'cyrille grandval'];
        yield 'espaces en tête et tabulation' => [" \tCyrille\t GRANDVAL", 'cyrille grandval'];
    }

    public function testGroupsSpeakerRowsByPersonne(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 1, 'helene-schapira', 'Hélène', 'SCHAPIRA', 10, '2019-10-24'),
            self::appearance(2, 1, 'helene-schapira', 'H.', 'Schapira-Martin', 20, '2021-10-14'),
            self::appearance(3, 2, 'benjamin-moules', 'Benjamin', 'MOULÈS', 30, '2019-10-24'),
            // Même libellé qu'une ligne de la personne 1, mais une autre personne
            self::appearance(4, 3, 'helene-schapira-2', 'Hélène', 'SCHAPIRA', 40, '2022-10-13'),
            // Deux talks pour la même ligne
            self::appearance(3, 2, 'benjamin-moules', 'Benjamin', 'MOULÈS', 31, '2019-10-25'),
        ]);

        // Triées sur le nom le plus récent : « Schapira-Martin » après « Schapira »
        self::assertSame(['benjamin-moules', 'helene-schapira-2', 'helene-schapira'], self::slugs($resolver));
        self::assertSame([2, 1], $resolver->getIdentityBySlug('helene-schapira')?->speakerIds);
        self::assertSame([3], $resolver->getIdentityBySlug('benjamin-moules')?->speakerIds);
        self::assertSame([4], $resolver->getIdentityBySlug('helene-schapira-2')?->speakerIds);
    }

    public function testSlugComesFromTheDatabaseAndNameFromTheMostRecentSpeaker(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 7, 'fred-dupont', 'Fred', 'Dupont', 10, '2015-10-24'),
            self::appearance(2, 7, 'fred-dupont', 'Frédéric', 'Dupont', 20, '2023-10-12'),
        ]);

        self::assertNull($resolver->getIdentityBySlug('frederic-dupont'), 'le slug n\'est pas recalculé depuis le nom');
        $identity = $resolver->getIdentityBySlug('fred-dupont');
        self::assertNotNull($identity);
        self::assertSame('fred-dupont', $identity->slug);
        self::assertSame('Frédéric Dupont', $identity->getName());
        self::assertSame('Frédéric DUPONT', $identity->getLabel());
        self::assertSame([2, 1], $identity->speakerIds);
        self::assertSame('2023-10-12', $identity->latestTalkAt->format('Y-m-d'));
    }

    public function testLabelDesignatesThePersonneCarryingItOnAnyOfItsRows(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 7, 'frederic-dupont', 'Fred', 'Dupont', 10, '2015-10-24'),
            self::appearance(2, 7, 'frederic-dupont', 'Frédéric', 'Dupont', 20, '2023-10-12'),
        ]);

        self::assertSame('frederic-dupont', $resolver->getIdentityByLabel('Frédéric DUPONT')?->slug);
        self::assertSame('frederic-dupont', $resolver->getIdentityByLabel('Fred  DUPONT ')?->slug);
        self::assertSame('frederic-dupont', $resolver->getIdentityByLabel('Frederic dupont')?->slug);
        self::assertNull($resolver->getIdentityByLabel('Inconnu SPEAKER'));
    }

    public function testLabelCarriedBySeveralPersonnesDesignatesNoOne(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 5, 'jean-martin', 'Jean', 'Martin', 10, '2012-11-20'),
            self::appearance(2, 6, 'jean-martin-lyon', 'Jean', 'MARTIN ', 20, '2018-10-25'),
            self::appearance(3, 6, 'jean-martin-lyon', 'Jean-Paul', 'Martin', 30, '2020-10-25'),
        ]);

        self::assertSame(['jean-martin', 'jean-martin-lyon'], self::slugs($resolver));
        self::assertNull($resolver->getIdentityByLabel('Jean MARTIN'), 'un libellé porté par plusieurs personnes ne désigne personne');
        self::assertSame('jean-martin-lyon', $resolver->getIdentityByLabel('Jean-Paul MARTIN')?->slug);
    }

    public function testIdentitiesDoNotDependOnReadingOrder(): void
    {
        $appearances = [
            self::appearance(1, 1, 'helene-schapira', 'Hélène', 'SCHAPIRA', 10, '2019-10-24'),
            self::appearance(2, 1, 'helene-schapira', 'Helene', 'Schapira', 20, '2021-10-14'),
            self::appearance(3, 2, 'julien-durand', 'Julien', 'Durand', 30, '2016-10-27'),
            self::appearance(4, 3, 'julien-durand-2', 'Julien', 'Durand', 40, '2014-06-23'),
            self::appearance(5, 3, 'julien-durand-2', 'Julien', 'Durand', 50, '2024-10-10'),
            self::appearance(6, 4, 'benjamin-moules', 'Benjamin', 'MOULÈS', 60, '2022-10-13'),
            self::appearance(6, 4, 'benjamin-moules', 'Benjamin', 'MOULÈS', 70, '2022-10-14'),
        ];

        $expected = self::identities($this->resolver($appearances));

        self::assertEquals($expected, self::identities($this->resolver(array_reverse($appearances))));
        // Toutes les rotations de la liste
        for ($offset = 1, $count = count($appearances); $offset < $count; $offset++) {
            $rotated = [...array_slice($appearances, $offset), ...array_slice($appearances, 0, $offset)];
            self::assertEquals($expected, self::identities($this->resolver($rotated)));
        }
    }

    public function testIdentitiesAreSortedByLastnameThenFirstname(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 1, 'zoe-elan', 'Zoé', 'Élan', 10, '2019-10-24'),
            self::appearance(2, 2, 'anne-martin', 'Anne', 'Martin', 20, '2019-10-24'),
            self::appearance(3, 3, 'aline-martin', 'Aline', 'Martin', 30, '2019-10-24'),
            self::appearance(4, 4, 'paul-albert', 'Paul', 'Albert', 40, '2019-10-24'),
        ]);

        self::assertSame(['paul-albert', 'zoe-elan', 'aline-martin', 'anne-martin'], self::slugs($resolver));
    }

    public function testFindsTheIdentityOfASpeakerFromAnyEditionByItsPersonne(): void
    {
        $resolver = $this->resolver([
            self::appearance(1, 1, 'helene-schapira', 'Hélène', 'SCHAPIRA', 10, '2019-10-24'),
            self::appearance(2, 5, 'cyrille-grandval', 'Cyrille', 'Grandval', 20, '2018-10-25'),
        ]);

        // Une ligne speaker connue
        self::assertSame('helene-schapira', $resolver->getIdentityForSpeaker(self::speaker(1, 'Hélène', 'SCHAPIRA', 1))?->slug);
        // Une ligne d'une édition à venir, rattachée à la personne
        self::assertSame('cyrille-grandval', $resolver->getIdentityForSpeaker(self::speaker(99, 'C.', 'Grandval', 5))?->slug);
        // Une ligne non rattachée n'a pas de personne, même si son libellé est connu
        self::assertNull($resolver->getIdentityForSpeaker(self::speaker(100, 'Hélène', 'SCHAPIRA')));
        // Une personne sans talk publié
        self::assertNull($resolver->getIdentityForSpeaker(self::speaker(101, 'Inconnu', 'Speaker', 9)));

        self::assertNull($resolver->getIdentityBySlug('inconnu'));
    }

    public function testProfileConsolidatesTheMostRecentNonEmptyValues(): void
    {
        $old = self::speaker(1, 'Hélène', 'SCHAPIRA')
            ->setBiography('Ancienne bio')
            ->setPhoto('1.jpg')
            ->setTwitter('@helene_old')
            ->setLinkedin('helene-schapira');
        $middle = self::speaker(2, 'Helene', 'SCHAPIRA')
            ->setBiography('Bio intermédiaire')
            ->setPhoto('')
            ->setMastodon('https://phpc.social/@helene')
            ->setBluesky('');
        $latest = self::speaker(3, 'Hélène', 'Schapira', 1)
            ->setBiography('  ')
            ->setPhoto(null)
            ->setTwitter('helene');

        $speakerTalks = [
            self::speakerTalk($old, 10, '2017-10-26'),
            self::speakerTalk($latest, 30, '2023-10-12', 'hschapira'),
            self::speakerTalk($middle, 20, '2020-10-22'),
            self::speakerTalk($middle, 21, '2020-10-23'),
            // La même personne deux fois sur un même talk
            self::speakerTalk($old, 21, '2020-10-23'),
        ];

        $publishedTalkRepository = $this->createMock(PublishedTalkRepository::class);
        $publishedTalkRepository->method('getSpeakerAppearances')->willReturn(array_map(
            static fn(SpeakerTalk $speakerTalk): array => self::appearance(
                (int) $speakerTalk->speaker->getId(),
                1,
                'helene-schapira',
                (string) $speakerTalk->speaker->getFirstname(),
                (string) $speakerTalk->speaker->getLastname(),
                $speakerTalk->talk->id,
                $speakerTalk->startsAt->format('Y-m-d'),
            ),
            $speakerTalks,
        ));
        $publishedTalkRepository->expects(self::once())
            ->method('getSpeakerTalks')
            ->with([3, 2, 1])
            ->willReturn(array_map(static fn(SpeakerTalk $speakerTalk): array => [
                'speaker_id' => (int) $speakerTalk->speaker->getId(),
                'talk' => $speakerTalk->talk,
                'event' => $speakerTalk->event,
                'starts_at' => $speakerTalk->startsAt,
                'github_login' => $speakerTalk->githubLogin,
            ], $speakerTalks));
        // Chaque ligne speaker est chargée une seule fois, quel que soit son nombre de talks.
        $speakerRepository = $this->createMock(SpeakerRepository::class);
        $speakerRepository->expects(self::exactly(3))->method('get')->willReturnMap([[3, $latest], [2, $middle], [1, $old]]);

        $profile = new SpeakerProfileResolver($publishedTalkRepository, $speakerRepository)->getProfileBySlug('helene-schapira');

        self::assertNotNull($profile);
        self::assertSame('helene-schapira', $profile->identity->slug);
        self::assertSame($latest, $profile->speaker);
        self::assertSame('Bio intermédiaire', $profile->biography);
        self::assertSame($old, $profile->photoSpeaker);
        self::assertSame([
            'twitter' => 'https://x.com/helene',
            'mastodon' => 'https://phpc.social/@helene',
            'linkedin' => 'https://www.linkedin.com/in/helene-schapira',
            'github' => 'https://github.com/hschapira',
        ], $profile->socialLinks);
        self::assertSame([30, 21, 20, 10], array_map(static fn(SpeakerTalk $speakerTalk): int => $speakerTalk->talk->id, $profile->talks));
    }

    public function testNoProfileForAnUnknownSlug(): void
    {
        $publishedTalkRepository = $this->createMock(PublishedTalkRepository::class);
        $publishedTalkRepository->method('getSpeakerAppearances')->willReturn([]);
        $publishedTalkRepository->expects(self::never())->method('getSpeakerTalks');

        self::assertNull(new SpeakerProfileResolver($publishedTalkRepository, $this->createStub(SpeakerRepository::class))->getProfileBySlug('inconnu'));
    }

    /**
     * @param list<array{speaker_id: int, personne_id: int, slug: string, firstname: string, lastname: string, talk_id: int, starts_at: int}> $appearances
     */
    private function resolver(array $appearances): SpeakerProfileResolver
    {
        $publishedTalkRepository = $this->createStub(PublishedTalkRepository::class);
        $publishedTalkRepository->method('getSpeakerAppearances')->willReturn($appearances);

        return new SpeakerProfileResolver($publishedTalkRepository, $this->createStub(SpeakerRepository::class));
    }

    /**
     * @return array{speaker_id: int, personne_id: int, slug: string, firstname: string, lastname: string, talk_id: int, starts_at: int}
     */
    private static function appearance(int $speakerId, int $personneId, string $slug, string $firstname, string $lastname, int $talkId, string $date): array
    {
        return [
            'speaker_id' => $speakerId,
            'personne_id' => $personneId,
            'slug' => $slug,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'talk_id' => $talkId,
            'starts_at' => new \DateTimeImmutable($date)->getTimestamp(),
        ];
    }

    private static function speaker(int $id, string $firstname, string $lastname, ?int $personneId = null): Speaker
    {
        return new Speaker()->setId($id)->setFirstname($firstname)->setLastname($lastname)->setPersonneId($personneId);
    }

    private static function speakerTalk(Speaker $speaker, int $talkId, string $date, ?string $githubLogin = null): SpeakerTalk
    {
        return new SpeakerTalk($speaker, new PublishedTalk($talkId, 'Talk ' . $talkId), new PublishedEvent(1, 'Forum PHP', 'forumphp'), new \DateTimeImmutable($date), $githubLogin);
    }

    /**
     * @return list<string>
     */
    private static function slugs(SpeakerProfileResolver $resolver): array
    {
        return array_map(static fn(SpeakerIdentity $identity): string => $identity->slug, $resolver->getIdentities());
    }

    /**
     * @return list<SpeakerIdentity>
     */
    private static function identities(SpeakerProfileResolver $resolver): array
    {
        return $resolver->getIdentities();
    }
}
