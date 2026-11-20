<?php

declare(strict_types=1);

namespace AppBundle\IntegrationTests\CFP;

use Afup\Tests\Support\IntegrationTestCase;
use AppBundle\CFP\SpeakerFactory;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Entity\GithubUser;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class SpeakerFactoryTest extends IntegrationTestCase
{
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = self::getContainer()->get(Connection::class);
        $this->connection->insert('afup_forum', ['id' => 1, 'titre' => 'Forum 2024', 'date_debut' => '2024-11-01']);
        $this->connection->insert('afup_forum', ['id' => 2, 'titre' => 'Forum 2025', 'date_debut' => '2025-11-01']);
        $this->connection->insert('afup_conferenciers_personnes', ['id' => 1, 'slug' => 'marie-curie']);
        // Édition passée, saisie via le CFP avec le compte GitHub 7
        $this->insertSpeaker(10, 1, 7, 1);
        $this->logInAsGithubUser(7);
    }

    public function testReusesTheRowOfThePersonneCreatedByTheAdminWithoutGithubAccount(): void
    {
        $this->insertSpeaker(20, 2, null, 1);

        $speaker = self::getContainer()->get(SpeakerFactory::class)->getSpeaker($this->event(2));

        self::assertSame(20, $speaker->getId());
        self::assertSame(7, (int) $this->connection->fetchOne('SELECT user_github FROM afup_conferenciers WHERE conferencier_id = 20'), 'le compte GitHub est rattaché à la ligne admin');
        self::assertSame(2, (int) $this->connection->fetchOne('SELECT COUNT(*) FROM afup_conferenciers'), 'aucune ligne créée');
    }

    public function testCreatesANewRowLinkedToThePersonneOfTheGithubAccount(): void
    {
        $speaker = self::getContainer()->get(SpeakerFactory::class)->getSpeaker($this->event(2));

        self::assertNull($speaker->getId());
        self::assertSame(7, $speaker->getUser());
        self::assertSame(1, $speaker->getPersonneId());
    }

    public function testReturnsTheExistingRowOfTheGithubAccount(): void
    {
        $this->insertSpeaker(21, 2, 7, null);
        $this->insertSpeaker(20, 2, null, 1);

        self::assertSame(21, self::getContainer()->get(SpeakerFactory::class)->getSpeaker($this->event(2))->getId());
    }

    private function logInAsGithubUser(int $id): void
    {
        $user = new GithubUser();
        $user->setId($id);
        self::getContainer()->get(TokenStorageInterface::class)->setToken(new UsernamePasswordToken($user, 'github'));
    }

    private function event(int $id): Event
    {
        return new Event()->setId($id);
    }

    private function insertSpeaker(int $id, int $eventId, ?int $githubUserId, ?int $personneId): void
    {
        $this->connection->insert('afup_conferenciers', [
            'conferencier_id' => $id,
            'id_forum' => $eventId,
            'prenom' => 'Marie',
            'nom' => 'Curie',
            'email' => 'marie@example.com',
            'user_github' => $githubUserId,
            'personne_id' => $personneId,
        ]);
    }
}
