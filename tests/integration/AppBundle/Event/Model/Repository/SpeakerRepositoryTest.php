<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\Event\Model\Repository;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Speaker\SpeakerProfileResolver;
use AppBundle\Event\Speaker\SpeakerTalk;
use Doctrine\DBAL\Connection;

final class SpeakerRepositoryTest extends IntegrationTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = self::getContainer()->get(Connection::class);

        $this->connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum PHP 2019', 'path' => 'forumphp2019', 'date_debut' => '2019-10-24', 'date_fin' => '2019-10-25', 'nb_places' => 100]);
        $this->connection->insert('afup_forum', ['id' => 2, 'titre' => 'Forum PHP 2021', 'path' => 'forumphp2021', 'date_debut' => '2021-10-14', 'date_fin' => '2021-10-15', 'nb_places' => 100]);
        $this->connection->insert('afup_user_github', ['id' => 7, 'github_id' => 1007, 'login' => 'hschapira', 'profile_url' => '', 'avatar_url' => '', 'afup_crew' => 0]);

        $this->connection->insert('afup_speaker_person', ['id' => 1, 'slug' => 'helene-schapira']);
        $this->connection->insert('afup_speaker_person', ['id' => 2, 'slug' => 'benjamin-moules']);

        $this->insertSpeaker(10, 1, 'Hélène', 'SCHAPIRA', null, 1);
        $this->insertSpeaker(20, 2, 'Helene', 'SCHAPIRA ', 7, 1);
        $this->insertSpeaker(30, 2, 'Benjamin', 'MOULÈS', null, 2);
        // Homonyme non rattaché à une personne : pas de page, même avec un talk publié
        $this->insertSpeaker(40, 1, 'Hélène', 'SCHAPIRA', null, null);

        $past = strtotime('2019-10-24 10:00:00');
        $this->insertTalk(100, 1, 10, 'Talk publié 2019', Talk::TYPE_FULL_LONG, $past);
        $this->insertTalk(200, 2, 20, 'Talk publié 2021', Talk::TYPE_FULL_LONG, strtotime('2021-10-14 10:00:00'), publishedOn: '2021-11-01 00:00:00');
        $this->insertTalk(201, 2, 20, 'Clinique', Talk::TYPE_CLINIC, $past);
        $this->insertTalk(202, 2, 20, 'Présentation des speakers', Talk::TYPE_SPEAKER_INTRODUCTIONS, $past);
        $this->insertTalk(203, 2, 20, 'Non planifié', Talk::TYPE_FULL_LONG, $past, scheduled: false);
        $this->insertTalk(204, 2, 20, 'Publication à venir', Talk::TYPE_FULL_LONG, $past, publishedOn: '+1 day');
        $this->insertTalk(205, 2, 20, 'Créneau pas encore terminé', Talk::TYPE_FULL_LONG, time() - 600, slotEnd: time() + 600);
        $this->insertTalk(206, 2, 20, 'Sans créneau', Talk::TYPE_FULL_LONG, null);
        $this->insertTalk(300, 2, 30, 'Talk à venir', Talk::TYPE_FULL_LONG, strtotime('+1 month'));
        $this->insertTalk(400, 1, 40, 'Talk publié sans personne', Talk::TYPE_FULL_LONG, $past);
    }

    public function testGetPublishedTalkAppearancesOnlyReturnsPublishedTalksOfLinkedSpeakers(): void
    {
        $appearances = self::getContainer()->get(SpeakerRepository::class)->getPublishedTalkAppearances();

        usort($appearances, static fn(array $a, array $b): int => $a['talk_id'] <=> $b['talk_id']);
        self::assertSame([
            ['speaker_id' => 10, 'person_id' => 1, 'slug' => 'helene-schapira', 'firstname' => 'Hélène', 'lastname' => 'SCHAPIRA', 'talk_id' => 100, 'starts_at' => strtotime('2019-10-24 10:00:00')],
            ['speaker_id' => 20, 'person_id' => 1, 'slug' => 'helene-schapira', 'firstname' => 'Helene', 'lastname' => 'SCHAPIRA ', 'talk_id' => 200, 'starts_at' => strtotime('2021-10-14 10:00:00')],
        ], $appearances);
    }

    public function testGetPublishedSpeakerTalksHydratesTalksEventsAndGithubLogins(): void
    {
        $speakerTalks = self::getContainer()->get(SpeakerRepository::class)->getPublishedSpeakerTalks([10, 20, 30]);

        self::assertSame(
            [
                [200, 'Talk publié 2021', 20, 'Forum PHP 2021', 'forumphp2021', 'hschapira', '2021-10-14 10:00'],
                [100, 'Talk publié 2019', 10, 'Forum PHP 2019', 'forumphp2019', null, '2019-10-24 10:00'],
            ],
            array_map(static fn(SpeakerTalk $speakerTalk): array => [
                $speakerTalk->talk->getId(),
                $speakerTalk->talk->getTitle(),
                $speakerTalk->speaker->getId(),
                $speakerTalk->event->getTitle(),
                $speakerTalk->event->getPath(),
                $speakerTalk->githubLogin,
                $speakerTalk->startsAt->format('Y-m-d H:i'),
            ], $speakerTalks),
        );
        self::assertSame([], self::getContainer()->get(SpeakerRepository::class)->getPublishedSpeakerTalks([]));
    }

    public function testResolverBuildsTheProfileOfASpeakerAcrossEditions(): void
    {
        $resolver = new SpeakerProfileResolver(self::getContainer()->get(SpeakerRepository::class));

        self::assertSame(['helene-schapira'], array_map(static fn($identity): string => $identity->slug, $resolver->getIdentities()));
        self::assertSame('helene-schapira', $resolver->getIdentityByLabel('Hélène SCHAPIRA')?->slug);

        $profile = $resolver->getProfileBySlug('helene-schapira');
        self::assertNotNull($profile);
        self::assertSame([20, 10], $profile->identity->speakerIds);
        self::assertSame('Bio de 20', $profile->biography);
        self::assertSame(['github' => 'https://github.com/hschapira'], $profile->socialLinks);
        self::assertSame([200, 100], array_map(static fn(SpeakerTalk $speakerTalk): ?int => $speakerTalk->talk->getId(), $profile->talks));
    }

    private function insertSpeaker(int $id, int $eventId, string $firstname, string $lastname, ?int $githubUserId, ?int $personId): void
    {
        $this->connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => $firstname,
            'nom' => $lastname,
            'email' => 'speaker' . $id . '@example.com',
            'biographie' => 'Bio de ' . $id,
            'user_github' => $githubUserId,
            'person_id' => $personId,
        ]);
    }

    private function insertTalk(int $id, int $eventId, int $speakerId, string $title, int $type, ?int $slotStart, bool $scheduled = true, ?string $publishedOn = null, ?int $slotEnd = null): void
    {
        $this->connection->insert('afup_sessions', [
            'session_id' => $id,
            'id_forum' => $eventId,
            'date_soumission' => '2019-01-01',
            'titre' => $title,
            'abstract' => '',
            'genre' => $type,
            'plannifie' => (int) $scheduled,
            'date_publication' => null !== $publishedOn ? date('Y-m-d H:i:s', (int) strtotime($publishedOn)) : null,
        ]);
        $this->connection->insert('afup_conferenciers_sessions', ['session_id' => $id, 'conferencier_id' => $speakerId]);

        if (null !== $slotStart) {
            $this->connection->insert('afup_forum_planning', ['id_session' => $id, 'debut' => $slotStart, 'fin' => $slotEnd ?? $slotStart + 2400, 'id_forum' => $eventId]);
        }
    }
}
